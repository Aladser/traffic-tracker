/** Клиентский табличный контроллер */
class TableClientController {
    /**
     * @param {*} URL URL бэк-контроллера
     * @param {*} table  таблица тем
     * @param {*} msgElement инфоэлемент
     * @param {*} form форма добавления элемента
     * @param {*} csrfToken csrf-токен
     */
    constructor(URL, table, msgElement, form) {
        this.URL = URL;
        this.table = table;
        this.msgElement = msgElement;
        this.form = form;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]');

        // таблица
        if (this.table !== null) {
            this.table
                .querySelectorAll(`.${this.table.id}__tr`)
                .forEach((row) => (row.onclick = (e) => this.clickRow(e)));
        }

        // форма добавления нового элемента
        if (this.form !== null) {
            this.form.onsubmit = (event) => this.add(form, event);
        }
    }

    async add(form, event) {
        event.preventDefault();
        let formData = new FormData(form);
        let headers = {
            "X-CSRF-TOKEN": this.csrfToken.getAttribute("content"),
        };
        let response = await fetch(this.URL, {
            method: "post",
            headers: headers,
            body: formData,
        });
        switch (response.status) {
            case 200:
                let data = await response.json();
                if (data.result == 1) {
                    this.processData(data.row, form);
                } else {
                    this.msgElement.textContent = data.description;
                }
                break;
            case 419:
                window.open("/wrong-uri", "_self");
                break;
            default:
                this.msgElement.textContent =
                    "Серверная ошибка. Подробности в консоли браузера";
                console.log(response);
        }
    }

    async remove(row) {
        let headers = {
            "X-CSRF-TOKEN": this.csrfToken.getAttribute("content"),
        };

        let response = await fetch(`${this.URL}/${row.id}`, {
            method: "delete",
            headers: headers,
        });
        switch (response.status) {
            case 200:
                let data = await response.json();
                if (data.result == 1) {
                    row.remove();
                    this.msgElement.textContent = "";
                } else {
                    this.msgElement.textContent = data;
                }
                break;
            case 419:
                window.open("/wrong-uri", "_self");
                break;
            default:
                this.msgElement.textContent =
                    "Серверная ошибка. Подробности в консоли браузера";
                console.log(response);
        }
    }

    /** клик строки */
    clickRow = (e) => this.click(e.target.closest("tr"));

    /** обработчик клика */
    click(row) {
        // клик на активную строку
        if (row.classList.contains(`${this.table.id}__tr--active`)) {
            row.classList.remove(`${this.table.id}__tr--active`);
            row.querySelector("button").remove();
        } else {
            // поиск активной строки
            let activeRow = this.table.querySelector(
                `.${this.table.id}__tr--active`
            );
            if (activeRow) {
                activeRow.querySelector("button").remove();
                activeRow.classList.remove(`${this.table.id}__tr--active`);
            }
            // выделение строки
            row.innerHTML += `<button id='${this.table.id}__btn-remove' title='Удалить'>🗑</button>`;
            row.lastChild.onclick = (e) => this.remove(e.target.closest("tr"));
            row.classList.add(`${this.table.id}__tr--active`);
        }
    }

    /** действия после добавления данных БД */
    processData(data, form) {
        alert("нет реализации метода processData класса TableFrontController");
    }
}
