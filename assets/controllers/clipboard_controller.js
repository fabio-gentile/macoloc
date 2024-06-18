import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ['button', 'svg']
  static values = {
    link: String,
  }

  copy() {
    const svg = this.svgTarget.outerHTML
    const text = this.linkValue
    const button = this.buttonTarget

    button.disabled = true
    button.innerHTML = 'Copié'

    navigator.clipboard.writeText(text)
      .then(() => {
        setTimeout(() => {
          button.innerHTML = svg
          button.disabled = false
        }, 2000);
      })
      .catch(err => {
        console.error(err)
        button.disabled = false
      });
  }
}
