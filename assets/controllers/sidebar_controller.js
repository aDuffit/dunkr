import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['container', 'label', 'iconClosed', 'iconOpen']

    connect() {
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            this.collapse();
        }
    }

    toggle() {
        if (this.containerTarget.classList.contains('w-64')) {
            this.collapse();
        } else {
            this.expand();
        }
    }

    collapse() {
        this.containerTarget.classList.replace('w-64', 'w-20');
        this.labelTargets.forEach(el => el.classList.add('hidden'));

        // Toggle les icônes
        this.iconOpenTarget.classList.add('hidden');
        this.iconClosedTarget.classList.remove('hidden');

        localStorage.setItem('sidebar-collapsed', 'true');
    }

    expand() {
        this.containerTarget.classList.replace('w-20', 'w-64');
        this.labelTargets.forEach(el => el.classList.remove('hidden'));

        // Toggle les icônes
        this.iconOpenTarget.classList.remove('hidden');
        this.iconClosedTarget.classList.add('hidden');

        localStorage.setItem('sidebar-collapsed', 'false');
    }
}
