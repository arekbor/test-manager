import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["button"];

  startLoading = () => {
    this.buttonTarget.classList.add("loading");
  };
}
