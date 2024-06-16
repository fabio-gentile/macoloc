import { startStimulusApp } from '@symfony/stimulus-bundle';
import Carousel from '@stimulus-components/carousel'
import Dialog from '@stimulus-components/dialog'
import Dropdown from '@stimulus-components/dropdown'

const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
app.register('carousel', Carousel);
app.register('dialog', Dialog);
app.register('dropdown', Dropdown);
