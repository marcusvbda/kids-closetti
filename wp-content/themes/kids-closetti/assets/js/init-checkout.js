window.createApp = (element, payload) => {
  const app = Vue.createApp(payload);
  app.mount(element);
  window.app = app;
};

window.startWebApp = () => {
  if (window?.webAppStated) {
    return;
  }

  const showHtml = () => {
    const html = document.querySelector("html");
    html.style.visibility = "visible";
  };

  showHtml();

  window.webAppStated = true;
};

document.addEventListener("DOMContentLoaded", () => startWebApp());
