import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["buttonCopyToClipboard"];

  static values = {
    url: String,
  };

  handleCopyToClipboard = () => {
    this.buttonCopyToClipboardTarget.disabled = true;
    navigator.clipboard.writeText(this.urlValue);

    setTimeout(() => {
      this.buttonCopyToClipboardTarget.disabled = false;
    }, 1500);
  };
}
