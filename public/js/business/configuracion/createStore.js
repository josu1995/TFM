const map = new google.maps.Map(document.getElementById("map"), {
    center: {
        lat: 40.4381311,
        lng: -3.8196229,
    },
    zoom: 5,
    disableDefaultUI: true,
    zoomControl: true,
    mapTypeControl:false,
    streetViewControl: false,
    gestureHandling: "cooperative",
});

const checkboxIsECommerce = document.querySelector(
    ".SelectableRow input[name='tiene_e_commerce']"
);
const radioRecogidasPeticion = document.querySelector(
    "#radio-button-recogidas-peticion"
);
const radioRecogidasAutomatizadas = document.querySelector(
    "#radio-button-recogidas-automatizadas"
);

const checkboxIsQCommerce = document.querySelector(
    ".SelectableRow input[name='tiene_q_commerce']"
);
const checkboxIsClickAndCollect = document.querySelector(
    ".SelectableRow input[name='es_almacen_q_commerce_click_and_collect']"
);
const checkboxIsDelivery = document.querySelector(
    ".SelectableRow input[name='es_almacen_q_commerce_delivery']"
);

const inputECommerceType = document.querySelector(
    ".RowContainer input[name='tipo_recogida_e_commerce_id']"
);

const daysContainer = document.querySelector("#days-container");
const daysButtons = document.querySelectorAll(".DaysContainer .DayBtn");
const inputDaysECommerce = document.querySelector(
    ".RowContainer input[name='dias_e_commerce']"
);

const containerECommerce = document.querySelector("#container-e-commerce");
const containerQCommerce = document.querySelector("#container-q-commerce");

const inputPostalCodeId = document.querySelector(
    ".ModalCreateStore input[name='codigo_postal_id']"
);
const inputPostalCodeText = document.querySelector(
    ".ModalCreateStore input[name='codigo_postal_text']"
);
const autocompleteResultsContainer = document.querySelector(
    "#autocomplete-cp-results"
);
const autocompleteWrapper = document.querySelector("#autocomplete-wrapper");
const newStoreImagePicker = document.querySelector("#NewStoreImagePicker");
const newStoreImagePreview = document.querySelector("#NewStoreImagePreview");
const deliverySchedule = document.querySelector(".SelectableRow [data-toggle='tiempo_q_commerce']");

class Autocomplete {
    hiddenInput;
    textInput;
    resultsContainer;
    wrapperDiv;

    static async #fetchResults(query) {
        const response = await fetch(`/codigos-postales/search?t=${query}&p=64`);
        return response.json();
    }

    static #hideResultsContainer() {
        this.resultsContainer.style.display = "none";
    }

    static #addEventListenerToResultRow(span, postal_code_id) {
        span.addEventListener("click", (e) => {
            this.textInput.value = e.target.innerText;
            this.hiddenInput.value = postal_code_id;
            this.#hideResultsContainer();
        });
    }

    static #renderResults(results, needle) {
        this.resultsContainer.innerHTML = "";

        if (!results.length) {
            this.#hideResultsContainer();
            return;
        }

        this.resultsContainer.style.display = "block";
        for (const result of results) {
            const span = document.createElement("span");
            span.className = "AutocompleteResultRow";
            const regex = new RegExp(needle, 'gi')
            const cityMatch = result.ciudad.match(regex)
            const zipCode = result.codigo_postal.replace(regex, `<span class="highlight">${needle}</span>`)
            const city = cityMatch ? result.ciudad.replace(regex, `<span class="highlight">${cityMatch[0]}</span>`) : result.ciudad
            span.innerHTML = `${zipCode} - ${city}`;
            this.#addEventListenerToResultRow(span, result.id);
            this.resultsContainer.appendChild(span);
        }
    }

    static #initListeners() {
        let timeout = null;
        this.textInput.addEventListener("keyup", (e) => {
            if (timeout) clearTimeout(timeout);

            timeout = setTimeout(async () => {
                const results = await this.#fetchResults(e.target.value);
                this.#renderResults(results, e.target.value);
            }, 200);
        });
        window.addEventListener("click", (e) => {
            if (!this.wrapperDiv.contains(e.target)) {
                this.#hideResultsContainer();
            }
        });
    }

    static init(hiddenInput, textInput, resultsContainer, wrapperDiv) {
        this.hiddenInput = hiddenInput;
        this.textInput = textInput;
        this.resultsContainer = resultsContainer;
        this.wrapperDiv = wrapperDiv;

        this.#initListeners();
    }
}

class Schedule {
    scheduleContainer;
    DAYS_LETTERS;
    scheduleData;
    hiddenInput;

    static init(scheduleContainer, hiddenInput) {
        this.scheduleContainer = scheduleContainer;
        this.hiddenInput = hiddenInput;
        this.DAYS_LETTERS = ["L", "M", "X", "J", "V", "S", "D"];
        this.scheduleData = {};
    }

    static #htmlStringToNode(html) {
        const template = document.createElement("template");
        html = html.trim();
        template.innerHTML = html;
        return template.content.firstChild;
    }

    static #updateHiddenInput() {
        const dataToWrite = [];

        for (const [day, daySchedule] of Object.entries(this.scheduleData)) {
            dataToWrite.push({
                dia: day,
                type: 'morning',
                inicio: daySchedule["start1"] || null,
                fin: daySchedule["end1"] || null,
            });
            dataToWrite.push({
                dia: day,
                type: 'afternoon',
                inicio: daySchedule["start2"] || null,
                fin: daySchedule["end2"] || null,
            });
        }
        this.hiddenInput.value = JSON.stringify(dataToWrite);
    }

    static #setDaySegmentClosed(
        dayNumber,
        isSegmentMorning,
        textValueNode = undefined
    ) {
        this.scheduleData[dayNumber] = {
            ...(this.scheduleData[dayNumber] || {}),
            ...(isSegmentMorning
                ? { start1: null, end1: null }
                : { start2: null, end2: null }),
        };
        this.#updateHiddenInput();
        if (textValueNode) {
            textValueNode.classList.add("Closed");
            textValueNode.innerText = "Cerrado";
        }
    }

    static #setDaySegment(
        dayNumber,
        isSegmentMorning,
        isOpenValue,
        value,
        textValueNode = undefined
    ) {
        this.scheduleData[dayNumber] = {
            ...(this.scheduleData[dayNumber] || {}),
            ...(isSegmentMorning
                ? isOpenValue
                    ? { start1: value }
                    : { end1: value }
                : isOpenValue
                    ? { start2: value }
                    : { end2: value }),
        };
        this.#updateHiddenInput();
        if (textValueNode) {
            textValueNode.classList.remove("Closed");
            const openValue =
                this.scheduleData[dayNumber][
                isSegmentMorning ? "start1" : "start2"
                ];
            const closeValue =
                this.scheduleData[dayNumber][
                isSegmentMorning ? "end1" : "end2"
                ];
            textValueNode.innerText = `${openValue || ""} - ${closeValue || ""
                }`;
        }
    }

    static addDay(dayNumber, start1, end1, start2, end2) {
        const hours = [
            '00:00', '00:15', '00:30', '00:45', '01:00', '01:15', '01:30', '01:45', '02:00', '02:15', '02:30', '02:45', '03:00',
            '03:15', '03:30', '03:45', '04:00', '04:15', '04:30', '04:45', '05:00', '05:15', '05:30', '05:45', '06:00', '06:15',
            '06:30', '06:45', '07:00', '07:15', '07:30', '07:45', '08:00', '08:15', '08:30', '08:45', '09:00', '09:15', '09:30',
            '09:45', '10:00', '10:15', '10:30', '10:45', '11:00', '11:15', '11:30', '11:45', '12:00', '12:15', '12:30', '12:45',
            '13:00', '13:15', '13:30', '13:45', '14:00', '14:15', '14:30', '14:45', '15:00', '15:15', '15:30', '15:45', '16:00',
            '16:15', '16:30', '16:45', '17:00', '17:15', '17:30', '17:45', '18:00', '18:15', '18:30', '18:45', '19:00', '19:15',
            '19:30', '19:45', '20:00', '20:15', '20:30', '20:45', '21:00', '21:15', '21:30', '21:45', '22:00', '22:15', '22:30',
            '22:45', '23:00', '23:15', '23:30', '23:45', '00:00'
        ];
        const isSegment1Closed = !start1 && !end1;
        const isSegment2Closed = !start2 && !end2;
        const timeArray = hours.map(h => `<option value="${h}">${h}</option>`);
        const openHourSelector = `<select class="form-control OpenInput RangeSelector"><option selected="true" disabled="disabled"></option>${timeArray}</select>`;
        const closeHourSelector = `<select class="form-control CloseInput RangeSelector"><option selected="true" disabled="disabled"></option>${timeArray}</select>`;

        const html = `<div class="Row">
                    <div class="Column Day">${this.DAYS_LETTERS[dayNumber - 1]}</div>
                    <div class="Column Open">
                        <span class="TextValue ${isSegment1Closed && "Closed"}">
                            ${!isSegment1Closed ? `${start1} - ${end1}` : "Cerrado"}
                        </span>
                        <div class="Selector Hidden">
                            ${openHourSelector}
                            ${closeHourSelector}
                            <button class="ClosedButton" type="button">
                                <i class="material-icons">do_not_disturb</i>
                            </button>
                        </div>
                    </div>
                    <div class="Column Close">
                        <span class="TextValue  ${isSegment2Closed && "Closed"}">
                            ${!isSegment2Closed ? `${start2} - ${end2}` : "Cerrado"}
                        </span>
                        <div class="Selector Hidden">
                            ${openHourSelector}
                            ${closeHourSelector}
                            <button class="ClosedButton" type="button">
                                <i class="material-icons">do_not_disturb</i>
                            </button>
                        </div>
                    </div>
                </div>`;

        const row = this.#htmlStringToNode(html);
        const columnOpen = row.querySelector(".Column.Open");
        const columnOpenSelector = columnOpen.querySelector(".Selector");
        const columnOpenTextValue = columnOpen.querySelector(".TextValue");

        const columnClose = row.querySelector(".Column.Close");
        const columnCloseSelector = columnClose.querySelector(".Selector");
        const columnCloseTextValue = columnClose.querySelector(".TextValue");

        columnOpen.addEventListener("click", (e) => {
            columnOpenSelector.classList.remove("Hidden");
        });
        columnOpen
            .querySelector(".OpenInput")
            .addEventListener("input", (e) =>
                this.#setDaySegment(
                    dayNumber,
                    true,
                    true,
                    e.target.value,
                    columnOpenTextValue
                )
            );
        columnOpen
            .querySelector(".CloseInput")
            .addEventListener("input", (e) =>
                this.#setDaySegment(
                    dayNumber,
                    true,
                    false,
                    e.target.value,
                    columnOpenTextValue
                )
            );
        columnOpen
            .querySelector(".ClosedButton")
            .addEventListener("click", (e) => {
                columnOpen.querySelector(".OpenInput").value = "";
                columnOpen.querySelector(".CloseInput").value = "";
                this.#setDaySegmentClosed(dayNumber, true, columnOpenTextValue);
            });

        columnClose.addEventListener("click", (e) => {
            columnCloseSelector.classList.remove("Hidden");
        });
        columnClose
            .querySelector(".OpenInput")
            .addEventListener("input", (e) =>
                this.#setDaySegment(
                    dayNumber,
                    false,
                    true,
                    e.target.value,
                    columnCloseTextValue
                )
            );
        columnClose
            .querySelector(".CloseInput")
            .addEventListener("input", (e) =>
                this.#setDaySegment(
                    dayNumber,
                    false,
                    false,
                    e.target.value,
                    columnCloseTextValue
                )
            );
        columnClose
            .querySelector(".ClosedButton")
            .addEventListener("click", (e) => {
                columnClose.querySelector(".OpenInput").value = "";
                columnClose.querySelector(".CloseInput").value = "";
                this.#setDaySegmentClosed(
                    dayNumber,
                    false,
                    columnCloseTextValue
                );
            });

        window.addEventListener("click", (e) => {
            if (!columnOpen.contains(e.target)) {
                columnOpenSelector.classList.add("Hidden");
            }
            if (!columnClose.contains(e.target)) {
                columnCloseSelector.classList.add("Hidden");
            }
        });

        this.#setDaySegment(dayNumber, true, true, start1);
        this.#setDaySegment(dayNumber, true, false, end1);
        this.#setDaySegment(dayNumber, false, true, start2);
        this.#setDaySegment(dayNumber, false, false, end2);

        this.scheduleContainer.appendChild(row);
    }
}

const E_COMMERCE_TYPES = {
    PETICION: 1,
    AUTOMATIZADA: 2,
};

const toggleContainerInteraction = (containerElement, active) => {
    containerElement.style.display = active ? "block" : "none";
};

checkboxIsECommerce.addEventListener("change", (e) => {
    if (e.target.checked && checkboxIsECommerce.hasAttribute('data-disabled')) {
        PNotify.removeAll()
        checkboxIsECommerce.checked = false
        new PNotify({
            title: 'Aviso',
            text: 'Sólo puede haber un almacén predeterminado para Entregas E-commerce',
            addclass: 'transporter-alert',
            icon: 'icon-transporter',
            autoDisplay: true,
        });
        return
    }
    toggleContainerInteraction(containerECommerce, e.target.checked)
});
checkboxIsQCommerce.addEventListener("change", (e) => {
    toggleContainerInteraction(containerQCommerce, e.target.checked);
    document.querySelector(".FloatingContainer").style.display = e.target.checked ? "block" : "none";
});

radioRecogidasPeticion.addEventListener("change", (e) => {
    if (e.target.checked) {
        radioRecogidasAutomatizadas.checked = false;
        inputECommerceType.value = E_COMMERCE_TYPES.PETICION;
        daysContainer.style.display = "none";
    }
});
radioRecogidasAutomatizadas.addEventListener("change", (e) => {
    if (e.target.checked) {
        radioRecogidasPeticion.checked = false;
        inputECommerceType.value = E_COMMERCE_TYPES.AUTOMATIZADA;
        daysContainer.style.display = "block";
    }
});

newStoreImagePicker.addEventListener('change', (event) => {
    if (!event.target.files.length) return

    const image = event.target.files[0]
    const reader = new FileReader()

    reader.readAsDataURL(image)
    reader.onload = e => newStoreImagePreview.src = e.target.result
    newStoreImagePreview.style.objectFit = "cover";
})

checkboxIsDelivery.addEventListener('change', (event) => {
    deliverySchedule.style.display = event.target.checked ? 'flex' : 'none'
})

daysButtons.forEach((dayButton) =>
    dayButton.addEventListener("click", (e) => {
        const wasActive = dayButton.classList.contains("Selected");
        dayButton.classList.toggle("Selected");

        const currentDays =
            inputDaysECommerce.value && inputDaysECommerce.value.trim()
                ? inputDaysECommerce.value.trim().split(",")
                : [];

        if (wasActive) {
            currentDays.splice(currentDays.indexOf(dayButton.innerText), 1);
        } else {
            currentDays.push(dayButton.innerText);
        }
        inputDaysECommerce.value = currentDays.join(",");
    })
);

let marker = null;
document.querySelector("#direccion-search-btn").addEventListener("click", e => {
    const addressInputValue = document.querySelector(".SearchBox input[name='direccion']")?.value || null;
    const inputPostalCodeTextValue = inputPostalCodeText.value || null;

    if (!addressInputValue || !inputPostalCodeTextValue) return;

    new google.maps.Geocoder().geocode(
        {
            address: `${addressInputValue}, ${inputPostalCodeTextValue.split('-')[0].trim()}, ${inputPostalCodeTextValue.split('-')[1].trim()}, ES`,
        },
        (results, status) => {
            if (status != google.maps.GeocoderStatus.OK || results[0].types[0] === 'postal_code' || results[0].types[0] === 'locality') {
                if (marker) marker.setMap(null);
                marker = null;
                return;
            }

            if (marker && (marker.position.lat() !== results[0].geometry.location.lat() || marker.position.lng() !== results[0].geometry.location.lng())) {
                marker.setMap(null);
                marker = null;
            }

            if (!marker || (marker && (marker.position.lat() !== results[0].geometry.location.lat() || marker.position.lng() !== results[0].geometry.location.lng()))) {
                marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location,
                    icon: '/img/maps/transporter-business-marker.png'
                });
                map.panTo({lat:marker.position.lat(),lng: marker.position.lng() - 0.001})
                map.setZoom(16);
            }
        }
    );
});

(function init() {
    daysContainer.style.display = "none";

    toggleContainerInteraction(containerECommerce, checkboxIsECommerce.checked);
    toggleContainerInteraction(containerQCommerce, checkboxIsQCommerce.checked);

    document.querySelector(".FloatingContainer").style.display = checkboxIsQCommerce.checked ? "block" : "none";

    Autocomplete.init(
        inputPostalCodeId,
        inputPostalCodeText,
        autocompleteResultsContainer,
        autocompleteWrapper
    );

    Schedule.init(
        document.getElementById("schedule-container"),
        document.getElementById("horarios-q-commerce")
    );
    for (let i = 1; i <= 7; i++) {
        Schedule.addDay(i, null, null, null, null);
    }
    // Schedule.addDay(1, "11:00", "14:00", "16:00", "20:00");
    // Schedule.addDay(2, "11:00", "14:00", "16:00", "20:00");
    // Schedule.addDay(3, null, null, "16:00", "20:00");
    // Schedule.addDay(4, "11:00", "14:00", "16:00", "20:00");
    // Schedule.addDay(5, "11:00", "14:00", null, null);
    // Schedule.addDay(6, "11:00", "14:00", "16:00", "20:00");
    // Schedule.addDay(7, "11:00", "14:00", "16:00", "20:00");
})();
