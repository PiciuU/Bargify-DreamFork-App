import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { library } from '@fortawesome/fontawesome-svg-core';

import { faUser, faLock, faBell, faRightFromBracket, faPenToSquare, faTrashCan, faClose, faSpinner } from '@fortawesome/free-solid-svg-icons';

library.add(faUser, faLock, faBell, faRightFromBracket, faPenToSquare, faTrashCan, faClose, faSpinner);

export const fontAwesome = {
    install: (app) => {
        app.component('font-awesome-icon', FontAwesomeIcon);
    },
};