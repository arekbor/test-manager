import { Controller } from "@hotwired/stimulus";

export default class VideoUploadController extends Controller {
  static targets = [
    "input",
    "dropzone",
    "progress",
    "spinner",
    "fileSelectionButton",
    "abortButton",
    "videoParagraph",
    "uploadImage",
    "previewFilename",
    "feedback",
  ];

  static values = {
    url: String,
    moduleId: String,
  };

  xhr = null;

  handleDragOver(e) {
    e.preventDefault();
    if (this.#isUploading()) {
      return;
    }

    this.#toggleDropzoneBackground(true);
  }

  handleAbort() {
    if (this.xhr) {
      this.xhr.abort();
    }
  }

  handleDragLeave(e) {
    e.preventDefault();
    if (this.#isUploading()) {
      return;
    }

    this.#toggleDropzoneBackground(false);
  }

  handleFileSelection() {
    if (this.#isUploading()) {
      return;
    }

    this.inputTarget.click();
  }

  handleFileInputChange(e) {
    if (this.#isUploading()) {
      return;
    }

    const file = e.target.files[0];
    if (file) {
      this.#uploadFile(file);
    }
  }

  async handleFileDrop(e) {
    e.preventDefault();
    if (this.#isUploading()) {
      return;
    }

    this.#toggleDropzoneBackground(false);

    const file = e.dataTransfer.files[0];
    if (!file) {
      throw new Error("No file found in the drop. Please try again.");
    }

    const items = e.dataTransfer.items;

    if (items[0]?.kind !== "file") {
      throw new Error(
        "The dropped item is not a file. Please make sure to drop a valid file."
      );
    }

    if (items[0].webkitGetAsEntry().isDirectory) {
      throw new Error(
        "Directories cannot be uploaded. Please select a single file."
      );
    }

    if (items.length > 1) {
      throw new Error(
        "You can only drop one file at a time. Please drop a single file."
      );
    }

    this.#uploadFile(file);
  }

  #uploadFile(file) {
    this.#updateUIForFileSelection(file.name);

    const formData = new FormData();
    formData.append("file", file);
    formData.append("moduleId", this.moduleIdValue);

    this.xhr = new XMLHttpRequest();

    this.xhr.upload.onprogress = (e) => {
      if (e.lengthComputable) {
        const percentComplete = (e.loaded / e.total) * 100;
        this.progressTarget.textContent = `${Math.round(percentComplete)}%`;
      }
    };

    this.xhr.onload = () => {
      this.#handleXhrResponse(this.xhr.response, this.xhr.status);
    };
    this.xhr.onerror = () => {
      this.#handleXhrResponse(this.xhr.response, this.xhr.status);
    };
    this.xhr.onabort = () => {
      this.#resetUIForFileSelection();
    };
    this.xhr.ontimeout = () => {
      this.#resetUIForFileSelection();
    };

    this.xhr.open("POST", this.urlValue, true);
    this.xhr.send(formData);
  }

  #handleXhrResponse(xhrResponse, xhrStatus) {
    this.feedbackTarget.classList.toggle("text-success", xhrStatus === 200);
    this.feedbackTarget.classList.toggle("text-danger", xhrStatus !== 200);

    const message = xhrResponse ? JSON.parse(xhrResponse)?.message : null;
    this.feedbackTarget.textContent = message || "Internal server error";

    this.#resetUIForFileSelection();
  }

  #isUploading() {
    return this.xhr && this.xhr.readyState > 0 && this.xhr.readyState < 4;
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
    const toggleClass = (element, add) => {
      element.classList[add ? "add" : "remove"]("d-none");
    };

    toggleClass(this.fileSelectionButtonTarget, !showDefault);
    toggleClass(this.videoParagraphTarget, !showDefault);
    toggleClass(this.uploadImageTarget, !showDefault);

    toggleClass(this.abortButtonTarget, showDefault);
    toggleClass(this.spinnerTarget, showDefault);
  }

  #toggleDropzoneBackground(toggle) {
    this.dropzoneTarget.style.backgroundColor = toggle ? "#e5e5e5" : "#f8f9fa";
  }
}
