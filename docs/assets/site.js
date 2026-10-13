(function () {
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
})();
