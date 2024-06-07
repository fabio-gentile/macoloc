import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['input', 'preview'];

  connect() {
    this.inputTarget.addEventListener('change', (event) => {
      this.previewImages(event);
    });
  }

  previewImages(event) {
    const files = event.target.files;
    this.previewTarget.innerHTML = '';

    Array.from(files).forEach(file => {
      const reader = new FileReader();

      reader.onload = (e) => {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.classList.add('preview-image');
        this.previewTarget.appendChild(img);
      };

      reader.readAsDataURL(file);
    });
  }
}
