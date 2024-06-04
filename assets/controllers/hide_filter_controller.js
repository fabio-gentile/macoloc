import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ["form"];

  openForm(e) {
    this.formTarget.classList.toggle("hidden");
  }
}
