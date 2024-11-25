import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static values = {
    uploadUrl: String,
  };

  static targets = ["progressBar", "buttonClose", "inputFile", "progress"];

  #xhr = null;

  handleCancel = () => {
    if (this.#xhr) {
      this.#xhr.abort();
    }
  };

  handleInputFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) {
      throw new Error("No file selected in the form.");
    }

    this.inputFileTarget.disabled = true;

    this.progressTarget.classList.remove("d-none");
    this.#xhr = new XMLHttpRequest();

    this.#xhr.onloadend = () => {
      this.buttonCloseTarget.click();
    };

    this.#xhr.upload.onprogress = (e) => {
      if (e.lengthComputable) {
        const percent = Math.round((e.loaded / e.total) * 100);
        this.progressBarTarget.style.width = `${percent}%`;
        this.progressBarTarget.textContent = `${percent}%`;
        this.progressBarTarget.ariaValueNow = percent;
      }
    };

    const formData = new FormData();
    formData.append("file", file);

    this.#xhr.open("POST", this.uploadUrlValue);
    this.#xhr.send(formData);
  };
}
