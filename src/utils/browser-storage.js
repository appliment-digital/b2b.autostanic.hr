export const session = {
    save: (key, data) => {
        sessionStorage.setItem(key, JSON.stringify(data));
    },
    load: (key) => {
        return JSON.parse(sessionStorage.getItem(key));
    },
};

export const local = {
    save: (key, data) => {
        localStorage.setItem(key, JSON.stringify(data));
    },
    load: (key) => {
        return JSON.parse(localStorage.getItem(key));
    },
};