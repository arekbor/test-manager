import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  connect = () => {
    this.observer = new MutationObserver(() => this.#checkForErrors());
    this.observer.observe(this.element, { childList: true, subtree: true });
  };

  disconnect = () => {
    this.observer.disconnect();
  };

  handleSubmit = () => {
    this.#checkForErrors();
  };

  #checkForErrors = () => {
    const errorElements = this.element.querySelectorAll(".invalid-feedback");
    if (errorElements.length > 0) {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    }
  };
}
