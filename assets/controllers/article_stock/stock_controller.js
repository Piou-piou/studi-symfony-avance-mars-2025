import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['stock'];

  decrease(event) {
    event.preventDefault();
    const link = event.currentTarget.dataset.href;

    this.#handle(link);
  }

  increase(event) {
    event.preventDefault();

    const link = event.currentTarget.dataset.href;
    this.#handle(link);
  }

  #handle(link) {
    fetch(link)
    .then(response => response.json())
    .then(data => {
      if (data.stock) {
        this.stockTarget.innerText = data.stock
      }
    });
  }
}
