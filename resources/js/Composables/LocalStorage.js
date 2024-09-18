export default function LocalStorage() {
    const store = (name, value, expires) => {
        if (expires === undefined || expires === 'null') {
            expires = 3600;
        } // default: 1h

        let date = new Date();
        let schedule = Math.round((date.setSeconds(date.getSeconds()+expires))/1000);

        window.localStorage.setItem(name, JSON.stringify(value));
        window.localStorage.setItem(name + '_time', schedule);
    };

    const get = (name) => {
        return JSON.parse(window.localStorage.getItem(name));
    };

    const remove = (name) => {
        window.localStorage.removeItem(name);
        window.localStorage.removeItem(name + '_time');
    };

    const status = (name) => {
        let date = new Date();
        let current = Math.round(+date / 1000);

        // Get Schedule
        let storedTime = window.localStorage.getItem(name + '_time');

        if (storedTime === undefined || storedTime === 'null') {
            storedTime = 0;
        }

        // Expired
        if (storedTime < current) {

            // Remove
            remove(name);

            return 0;
        } else {
            return 1;
        }
    };

    return {
        store,
        get,
        remove,
        status
    }
};
