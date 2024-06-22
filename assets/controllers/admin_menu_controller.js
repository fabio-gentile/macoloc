import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ['dropdown', 'sidebar', 'backdrop', 'header']

  connect() {
    this.outsideClickListener = this.handleOutsideClick.bind(this);
  }

  openDropdown() {
    this.dropdownTarget.classList.toggle('hidden');
  }

  toggleSidebar() {
    this.sidebarTarget.classList.toggle('-translate-x-full');
    this.sidebarTarget.classList.toggle('transform-none');
    document.querySelector('body').classList.toggle('overflow-hidden');
    this.backdropTarget.classList.toggle('hidden');

    if (!this.sidebarTarget.classList.contains('-translate-x-full')) {
      document.addEventListener('mousedown', this.outsideClickListener);
    } else {
      document.removeEventListener('mousedown', this.outsideClickListener);
    }
  }

  handleOutsideClick(event) {
    if (!this.sidebarTarget.contains(event.target) && !this.headerTarget.contains(event.target)) {
      this.toggleSidebar();
    }
  }
}
