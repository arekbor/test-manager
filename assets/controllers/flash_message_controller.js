import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  connect() {
    setTimeout(() => {
      this.element.classList.remove("show");

      setTimeout(() => {
        this.element.remove();
      }, 1000);
    }, 3000);
  }
}
