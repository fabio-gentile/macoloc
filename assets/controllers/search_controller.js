import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['address', 'input', 'form']

    static values = {
      from: String
    }

    connect() {
      this.updateForm();
    }

    open() {
      this.addressTarget.classList.remove('hidden');
    }

  close() {
    // Delay hiding to allow click event to process
    setTimeout(() => {
      this.addressTarget.classList.add('hidden');
    }, 100);
  }

  selectAddress(event) {
    event.stopPropagation();
    this.inputTarget.value = event.currentTarget.innerText;
    this.addressTarget.classList.add('hidden');
  }

    submitForm(e) {
      e.preventDefault();
      if (this.inputTarget.value.length < 3) {
        return false;
      }

      this.updateForm();
      this.formTarget.submit();
    }
    updateForm() {
      if (document.getElementById('searchType')) {
        const select = document.getElementById('searchType').value ;
        this.formTarget.action = `/search/${select}/${this.inputTarget.value}`;
      } else {
        this.formTarget.action = `/search/${this.fromValue}/${this.inputTarget.value}`;

      }
    }
}
