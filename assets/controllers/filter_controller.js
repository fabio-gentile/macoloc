import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ["form"];

  applyFilter(event) {
    this.formTarget.requestSubmit();
    // this.formTarget.submit();
  }
}
