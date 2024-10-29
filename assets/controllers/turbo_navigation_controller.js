import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  connect() {
    window.addEventListener("popstate", this.#wizardStepChanged);
  }

  #wizardStepChanged() {
    Turbo.visit(window.location.href, { action: "replace" });
  }
}
