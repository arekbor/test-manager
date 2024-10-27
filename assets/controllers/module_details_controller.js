import { Controller } from "@hotwired/stimulus";
import { Tab } from "bootstrap";

export default class ModuleDetailsController extends Controller {
  static targets = ["tabButton"];

  #localStorageKey = "last-tab";
  #buttonAttribute = "data-bs-target";

  connect() {
    this.#activateSavedTab();
    this.#addTabChangeListener();
  }

  #activateSavedTab() {
    const lastTab = localStorage.getItem(this.#localStorageKey);
    const activeTarget = lastTab
      ? this.tabButtonTargets.find(
          (element) => element.getAttribute(this.#buttonAttribute) === lastTab
        )
      : this.tabButtonTargets[0];

    if (!activeTarget) {
      throw new Error("Target element not found.");
    }

    console.log(activeTarget);

    new Tab(activeTarget).show();
  }

  #addTabChangeListener() {
    this.tabButtonTargets.forEach((element) => {
      element.addEventListener("click", () => {
        localStorage.setItem(
          this.#localStorageKey,
          element.getAttribute(this.#buttonAttribute)
        );
      });
    });
  }
}
