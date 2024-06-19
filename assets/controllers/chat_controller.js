import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['container']

  connect() {
    this.scrollToBottom();
    this.observer = new MutationObserver(this.scrollToBottom.bind(this));
    this.observer.observe(this.containerTarget, { childList: true, subtree: true });
  }

  scrollToBottom() {
    this.containerTarget.scrollTop = this.containerTarget.scrollHeight;
  }

  disconnect() {
    if (this.observer) {
      this.observer.disconnect();
    }
  }
}
