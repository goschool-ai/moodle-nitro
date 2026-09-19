(function () {
  // Remember an explicit language choice so the root no longer redirects.
  document.querySelectorAll("[data-lang]").forEach(function (a) {
    a.addEventListener("click", function () {
      try { localStorage.setItem("nitro-lang", a.getAttribute("data-lang")); } catch (e) {}
    });
  });

  // Copy the connector URL.
  document.querySelectorAll("[data-copy]").forEach(function (btn) {
    var label = btn.textContent;
    btn.addEventListener("click", function () {
      var text = document.getElementById(btn.getAttribute("data-copy")).textContent.trim();
      var done = function () {
        btn.textContent = btn.getAttribute("data-done");
        setTimeout(function () { btn.textContent = label; }, 1800);
      };
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(done, function () {});
      }
    });
  });

  // Live GitHub star count next to the star buttons.
  var counts = document.querySelectorAll("[data-stars]");
  if (counts.length && window.fetch) {
    fetch("https://api.github.com/repos/goschool-ai/moodle-nitro", { headers: { Accept: "application/vnd.github+json" } })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (repo) {
        if (!repo || typeof repo.stargazers_count !== "number" || repo.stargazers_count < 1) return;
        counts.forEach(function (el) {
          el.textContent = repo.stargazers_count;
          el.hidden = false;
        });
      })
      .catch(function () {});
  }

  // From 7 October 2026 (Budapest) the trial course is open: the button signs you up.
  var open = new Date("2026-10-07T00:00:00+02:00");
  if (new Date() >= open) {
    var root = document.documentElement;
    var hu = root.lang === "hu";
    var cta = document.querySelector('[data-state="cta"]');
    var sticker = document.querySelector('[data-state="sticker"]');
    if (cta) {
      cta.href = "https://moodle.tilosazai.org/login/signup.php";
      cta.textContent = hu ? "Kérj próbakurzust" : "Get a trial course";
    }
    if (sticker) sticker.textContent = hu ? "nyitva a próbakurzus" : "trial course open";
  }
})();
