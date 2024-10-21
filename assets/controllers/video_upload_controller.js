import { Controller } from "@hotwired/stimulus";

export default class VideoUploadController extends Controller {
  static values = {
    uploudUrl: String,
    moduleId: String,
  };

  static targets = [
    "switchable",
    "fileInput",
    "previewFilename",
    "messageFeedback",
    "dropzone",
    "progress",
  ];

  #xhr = null;

  handleDragOver = (e) => {
    e.preventDefault();

    if (this.#isFileUplouding) {
      return;
    }
  };

  handleDragLeave = (e) => {
    e.preventDefault();
  };

  handleFileSelection = () => {
    if (this.#isFileUplouding) {
      return;
    }

    this.fileInputTarget.click();
  };

  handleFileInputChange = (e) => {
    if (this.#isFileUplouding) {
      return;
    }

    const file = e.target.files[0];
    if (!file) {
      throw new Error("No file selected in the form.");
    }

    this.#uploadFile(file);
  };

  handleFileDrop = async (e) => {
    e.preventDefault();

    if (this.#isFileUplouding) {
      return;
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

    const file = e.dataTransfer.files[0];
    if (!file) {
      throw new Error("No file found in the drop. Please try again.");
    }

    this.#uploadFile(file);
  };

  handleAbort = () => {
    if (!this.#xhr) {
      throw new Error("Xhr was not initialized.");
    }

    this.#xhr.abort();
  };

  #uploadFile = (file) => {
    this.#toggleUI();
    this.messageFeedbackTarget.textContent = "";
    this.previewFilenameTarget.textContent = file.name;

    this.#xhr = new XMLHttpRequest();

    this.#xhr.upload.onprogress = (e) => {
      if (e.lengthComputable) {
        this.#updateProgress((e.loaded / e.total) * 100);
      }
    };

    this.#xhr.onloadend = () => {
      this.#toggleUI();

      const response = this.#xhr.response;
      const message = response ? JSON.parse(response)?.message : null;

      if (message) {
        this.messageFeedbackTarget.classList.toggle(
          "text-success",
          this.#xhr.status === 200
        );

        this.messageFeedbackTarget.classList.toggle(
          "text-danger",
          this.#xhr.status !== 200
        );

        this.messageFeedbackTarget.textContent = message;
      }
    };

    const formData = new FormData();
    formData.append("file", file);
    formData.append("moduleId", this.moduleIdValue);

    this.#xhr.open("POST", this.uploudUrlValue, true);
    this.#xhr.send(formData);
  };

  #toggleUI = () => {
    this.switchableTargets.forEach((target) => {
      target.classList.toggle("d-none");
    });
  };

  get #isFileUplouding() {
    return this.#xhr && this.#xhr.readyState > 0 && this.#xhr.readyState < 4;
  }

  #updateProgress = (progress) => {
    this.progressTarget.textContent = `${Math.round(progress)}%`;
  };
}
