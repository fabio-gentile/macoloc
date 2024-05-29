import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ["mobile", "overlay"]

  connect() {
    this.isOpened = false
  }

  toggleMenu() {
    this.isOpened = !this.isOpened
    this.updateMenuState()
  }

  closeMenu(event) {
    if (this.mobileTarget.contains(event.target)) {
      if (!event.target.getAttribute('data-close') === null) return
    }
    this.isOpened = false
    this.updateMenuState()
  }

  updateMenuState() {
    this.mobileTarget.classList.toggle("translate-x-0", this.isOpened)
    this.mobileTarget.classList.toggle("-translate-x-full", !this.isOpened)
    document.body.classList.toggle("overflow-hidden", this.isOpened)
    this.overlayTarget.classList.toggle("hidden", !this.isOpened)
    this.overlayTarget.classList.toggle("block", this.isOpened)
  }
}
