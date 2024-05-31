import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['address', 'input']

  selectAddress(event) {
    this.inputTarget.value = event.target.innerText
    this.addressTarget.replaceChildren()
  }
}
