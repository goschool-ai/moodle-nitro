#!/usr/bin/env python3
"""Builds the Copilot Cowork connector package (connector only, no skills) for one Moodle site.

    python3 copilot/build.py --site https://moodle.example.edu --name "Example University"
    python3 copilot/build.py --site ... --name ... --reference-id <OAuth registration ID from Agents Toolkit>

Without --reference-id the connector relies on dynamic client registration; with it, on an OAuth
client registered in the Microsoft Enterprise Token Store (OAuthPluginVault). The tool description is
taken from local/nitro/tests/fixtures/tools_list.json, which the PHPUnit snapshot test keeps equal to
what the plugin's tools/list returns.
"""
import argparse, json, pathlib, struct, uuid, zipfile, zlib

ROOT = pathlib.Path(__file__).resolve().parent.parent


def png(size, rgba):
    """A solid-colour PNG, as a placeholder icon."""
    raw = b''.join(b'\x00' + bytes(rgba) * size for _ in range(size))
    def chunk(kind, data):
        return struct.pack('>I', len(data)) + kind + data + struct.pack('>I', zlib.crc32(kind + data) & 0xffffffff)
    return (b'\x89PNG\r\n\x1a\n' + chunk(b'IHDR', struct.pack('>IIBBBBB', size, size, 8, 6, 0, 0, 0))
            + chunk(b'IDAT', zlib.compress(raw)) + chunk(b'IEND', b''))


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--site', required=True, help='Moodle wwwroot, for example https://moodle.example.edu')
    parser.add_argument('--name', required=True, help='Site name shown in Copilot')
    parser.add_argument('--reference-id', help='OAuthPluginVault registration ID; omit for dynamic registration')
    parser.add_argument('--out', default=str(ROOT / 'copilot' / 'build'))
    args = parser.parse_args()

    site = args.site.rstrip('/')
    authorization = ''
    if args.reference_id:
        authorization = (',\n          "authorization": {\n            "type": "OAuthPluginVault",\n'
                         f'            "referenceId": {json.dumps(args.reference_id)}\n          }}')
    manifest = (ROOT / 'copilot' / 'manifest.template.json').read_text()
    for key, value in {
        '{{APP_ID}}': str(uuid.uuid5(uuid.NAMESPACE_URL, site + '/local/nitro/mcp.php')),
        '{{SITE_NAME}}': args.name,
        '{{MCP_URL}}': site + '/local/nitro/mcp.php',
        '{{AUTHORIZATION}}': authorization,
    }.items():
        manifest = manifest.replace(key, value)
    json.loads(manifest)  # Fail early on a broken manifest.

    tools = json.loads((ROOT / 'local' / 'nitro' / 'tests' / 'fixtures' / 'tools_list.json').read_text())
    out = pathlib.Path(args.out)
    out.mkdir(parents=True, exist_ok=True)
    package = out / f"nitro-{uuid.uuid5(uuid.NAMESPACE_URL, site).hex[:8]}.zip"
    with zipfile.ZipFile(package, 'w', zipfile.ZIP_DEFLATED) as z:
        z.writestr('manifest.json', manifest)
        z.writestr('tools/nitro-tools.json', json.dumps({'tools': tools}, ensure_ascii=False, indent=2))
        z.writestr('color.png', png(192, (75, 46, 131, 255)))
        z.writestr('outline.png', png(32, (255, 255, 255, 255)))
    print(package)


if __name__ == '__main__':
    main()
