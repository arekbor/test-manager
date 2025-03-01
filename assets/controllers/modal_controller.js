import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["buttonClose", "buttonModalClose", "buttonAction"];

  handleAction = () => {
    this.buttonCloseTarget.setAttribute("disabled", "");
    this.buttonModalCloseTarget.setAttribute("disabled", "");
    this.buttonActionTarget.classList.add("loading");
  };
}
