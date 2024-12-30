import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static values = {
    clipboardLink: String,
  };

  static targets = ["buttonCopyToClipboard"];

  handleCopyToClipboard = async () => {
    try {
      await navigator.clipboard.writeText(this.clipboardLinkValue);

      this.buttonCopyToClipboardTarget.disabled = true;

      setTimeout(() => {
        this.buttonCopyToClipboardTarget.disabled = false;
      }, 500);
    } catch (err) {
      console.error("Failed to copy: ", err);
    }
  };
}
