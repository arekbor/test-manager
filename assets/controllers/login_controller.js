import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["submit"];

  startLoading = () => {
    this.submitTarget.classList.add("loading");
  };
}
