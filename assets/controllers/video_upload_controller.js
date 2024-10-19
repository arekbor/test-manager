import { Controller } from "@hotwired/stimulus";

export default class VideoUploadController extends Controller {
  static targets = [
    "input",
    "dropZone",
    "progress",
    "spinner",
    "chooseFileButton",
    "videoParagraph",
    "uploadImage",
    "previewFilename",
    "feedback",
  ];

  static values = {
    url: String,
    moduleId: String,
  };

  handleDragOver(e) {
    e.preventDefault();
  }

  handleDragLeave(e) {
    e.preventDefault();
  }

  handleClick() {
    this.inputTarget.click();
  }

  handleFileInputChange(e) {
    const file = e.target.files[0];
    if (file) {
      this.#uploadFile(file);
    }
  }

  async handleFileDrop(e) {
    e.preventDefault();

    const file =
      e.dataTransfer.items[0]?.kind === "file" ? e.dataTransfer.files[0] : null;
    if (
      !file ||
      e.dataTransfer.items.length > 1 ||
      !(await this.#isFile(file))
    ) {
      this.#displayFeedback("Invalid file. Please upload a valid file.", 400);
      return;
    }

    this.#uploadFile(file);
  }

  #uploadFile(file) {
    this.#updateUIForFileSelection(file.name);

    const formData = new FormData();
    formData.append("file", file);
    formData.append("moduleId", this.moduleIdValue);

    const xhr = new XMLHttpRequest();

    xhr.upload.onprogress = (e) => {
      if (e.lengthComputable) {
        const percentComplete = (e.loaded / e.total) * 100;
        this.progressTarget.textContent = `${Math.round(percentComplete)}%`;
      }
    };

    xhr.onload = () => {
      const response = JSON.parse(xhr.response);
      this.#displayFeedback(response.message, xhr.status);
    };

    xhr.onerror = () => {
      this.#displayFeedback("An error occurred during file upload.", 500);
    };

    xhr.open("POST", this.urlValue, true);
    xhr.send(formData);
  }

  #updateUIForFileSelection(filename) {
    this.#toggleUI(false);
    this.previewFilenameTarget.textContent = filename;
    this.feedbackTarget.textContent = "";
    this.progressTarget.textContent = "";
  }

  #resetUIForFileSelection() {
    this.#toggleUI(true);
    this.progressTarget.textContent = "";
    this.previewFilenameTarget.textContent = "";
  }

  #toggleUI(showDefault) {
    const action = showDefault ? "remove" : "add";
    this.chooseFileButtonTarget.classList[action]("d-none");
    this.videoParagraphTarget.classList[action]("d-none");
    this.uploadImageTarget.classList[action]("d-none");
    this.spinnerTarget.classList[showDefault ? "add" : "remove"]("d-none");
  }

  #displayFeedback(message, status) {
    this.feedbackTarget.classList.toggle("text-success", status === 200);
    this.feedbackTarget.classList.toggle("text-danger", status !== 200);
    this.feedbackTarget.textContent = message || "Internal server error";
    this.#resetUIForFileSelection();
  }

  async #isFile(file) {
    return new Promise((resolve) => {
      const fr = new FileReader();

      fr.onprogress = (e) => {
        if (e.loaded > 50) {
          fr.abort();
          resolve(true);
        }
      };

      fr.onload = () => resolve(true);
      fr.onerror = () => resolve(false);

      fr.readAsArrayBuffer(file);
    });
  }
}
