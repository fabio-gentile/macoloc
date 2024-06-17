import { Controller } from '@hotwired/stimulus';
import "leaflet/dist/leaflet.min.css"
import L from "leaflet"
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['container']
    static values = {
        lat: String,
        lon: String,
    }

    connect() {
      this.map = L.map(this.containerTarget).setView(this.coordinates(), 14);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(this.map);
    }

    coordinates(){
      return [this.latValue, this.lonValue]
    }

    disconnect(){
      this.map.remove()
    }
}
