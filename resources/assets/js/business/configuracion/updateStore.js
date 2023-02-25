const UpdateStore = () => {
  let debounce = null
  let storeMarker = null
  const gMap = new google.maps.Map(document.getElementById('gMap'), {
    center: { lat:  39.3705675, lng: -3.4201541 },
    zoom: 16,
    disableDefaultUI: false,
    zoomControl: true,
    mapTypeControl:false,
    streetViewControl: false,
    gestureHandling: 'cooperative',
  })
  const weekDays = ['L', 'M', 'X', 'J', 'V', 'S', 'D']
  const defaultSchedule = {
    L: { mor: { open: null, close: null }, aft: { open: null, close: null } },
    M: { mor: { open: null, close: null }, aft: { open: null, close: null } },
    X: { mor: { open: null, close: null }, aft: { open: null, close: null } },
    J: { mor: { open: null, close: null }, aft: { open: null, close: null } },
    V: { mor: { open: null, close: null }, aft: { open: null, close: null } },
    S: { mor: { open: null, close: null }, aft: { open: null, close: null } },
    D: { mor: { open: null, close: null }, aft: { open: null, close: null } },
  }

  /**
   *  STATE
   */
  const modelState = {
    map: null,
    store: null,
    modalOpened: false,
    loading: false,
    errors: [],

    zipCode: null,
    zipCodeID: null,
    address: null,
    hasECommerce: false,
    eCommType: null,
    eCommDays: [],
    eCommTimetable: '',
    hasQCommerce: false,
    qCommClicknCollect: false,
    qCommDelivery: false,
    qCommTimetable: 15,
    showSchedulerFor: null,
    qCommSchedule: { ...defaultSchedule },
    image: null,
    imagePreview: null,

    zipResults: []
  }

  /**
   *  ACTIONS
   */
  const modelActions = {

    // Open modal.
    openModal: function (content) {
      this.$nextTick(() => this.$refs.EditStoreModal.focus())

      $('body').addClass('modal-open')

      this._initializeStoreData(content)

      this.handleAddressSearch()

      setTimeout(() => { this.modalOpened = true }, 200)
    },

   
    // Close edit modal.
    closeModal: function (event) {
      if (!this.modalOpened) return

      $('body').removeClass('modal-open')
      this.modalOpened = false
      setTimeout(() => { this.store = null }, 200)
    },

    // Handle zipcode search.
    findZipCode: function async() {
      if (debounce) clearTimeout(debounce)

      debounce = setTimeout(async () => {
        const response = await fetch(`/codigos-postales/search?t=${this.zipCode}&p=64`)
        this.zipResults = await response.json()
      }, 300)
    },

    // Handle zipcode search.
    selectZipCode: function (zip) {
      this.zipCode = `${zip.codigo_postal} - ${zip.ciudad}`
      this.zipCodeID = zip.id
      this.zipResults = []
    },

    renderZipCodeMatch: function (result, needle) {
      const regex = new RegExp(needle, 'gi')
      const cityMatch = result.ciudad.match(regex)
      const zipCode = result.codigo_postal.replace(regex, `<span class="highlight">${needle}</span>`)
      const city = cityMatch ? result.ciudad.replace(regex, `<span class="highlight">${cityMatch[0]}</span>`) : result.ciudad

      return `${zipCode} - ${city}`
    },

    // Handle search for an address.
    handleAddressSearch: function (event) {
      if (!this.address || !this.zipCode) {
        return new PNotify({
          title: 'Incompleto',
          text: 'Debes introducir tu ciudad o C.P. y la dirección',
          addclass: 'transporter-alert',
          icon: 'icon-transporter',
          delay: 2500,
        })
      }

      const handleGeoCoderRequest = (results, status) => {
        if (status != google.maps.GeocoderStatus.OK || results[0].types[0] === 'postal_code' || results[0].types[0] === 'locality') {
          if (storeMarker) storeMarker.setMap(null)
          storeMarker = null
          return
        }

        if (storeMarker && (storeMarker.position.lat() !== results[0].geometry.location.lat() || storeMarker.position.lng() !== results[0].geometry.location.lng())) {
          storeMarker.setMap(null)
          storeMarker = null
        }

        if (!storeMarker || (storeMarker && (storeMarker.position.lat() !== results[0].geometry.location.lat() || storeMarker.position.lng() !== results[0].geometry.location.lng()))) {
          storeMarker = new google.maps.Marker({
            map: gMap,
            position: results[0].geometry.location,
            icon: '/img/maps/transporter-business-marker.png'
          })
          gMap.panTo(storeMarker.position)
          gMap.setZoom(16)
        }
      }
      const addressObj = { address: `${this.address}, ${this.zipCode.split('-')[0].trim()}, ${this.zipCode.split('-')[1].trim()}, ES` }
      new google.maps.Geocoder().geocode(addressObj, handleGeoCoderRequest)
    },

    // Handle change hasECommerce
    handleChangeHasECommerce: function () {
      if (!this.store?.hasDefaultEcomm || this.store?.hasDefaultEcomm === this.store?.id) return

      this.hasECommerce = false

      PNotify.removeAll()
      new PNotify({
        title: 'Aviso',
        text: 'Sólo puede haber un almacén predeterminado para Entregas E-commerce',
        addclass: 'transporter-alert',
        icon: 'icon-transporter',
        autoDisplay: true,
      });
    },

    // Handle image uploaded.
    handleImage: function (event) {
      if (!event.target.files.length) return

      this.image = event.target.files[0]
      const reader = new FileReader()

      reader.readAsDataURL(this.image)
      reader.onload = e => this.imagePreview = e.target.result
    },

    // Update store data.
    updateStore: async function () {
      this.loading = true
      this.errors = []

      let response = null
      try {
        response = await fetch(`/api/tbusiness/v1/configuracion/stores/${this.store.id}`, {
          method: 'POST',
          headers: { Accept: 'aplication/json' },
          body: this._getTransformedData()
        });
      } catch (error) {
        console.warn('Ups! There\'s been an error while trying to update the store data')
        this.errors = errors || []
        this.loading = false
        return;
      }
      response = await response.json();

      if (response.errors) {
        this.errors = Object.values(response.errors).map(v => v[0]);
        this.loading = false;
        return;
      }

      this.loading = false
      location.reload()
    },

    // Set up store data.
    _initializeStoreData: function (content) {
      this.store = content

      this.selectZipCode(content?.codigo_postal)
      this.address = content?.direccion
      this.image = this.imagePreview = content?.entregas_q_commerce?.imagen
      this.hasECommerce = !!content?.entregas_e_commerce
      this.eCommType = content?.entregas_e_commerce?.tipo_recogida_e_commerce_id
      this.eCommDays = content?.entregas_e_commerce?.dias?.split(',')
      this.eCommTimetable = content?.entregas_e_commerce?.franja_horaria_id

      this.hasQCommerce = !!content?.entregas_q_commerce
      this.qCommClicknCollect = !!content?.entregas_q_commerce?.es_almacen_click_and_collect
      this.qCommDelivery = !!content?.entregas_q_commerce?.es_almacen_delivery
      this.qCommTimetable = content?.entregas_q_commerce?.tiempo
      // this.qCommSchedule = defaultSchedule // This won't update qCommSchedule value.
      this.qCommSchedule = {
        L: { mor: { open: null, close: null }, aft: { open: null, close: null } },
        M: { mor: { open: null, close: null }, aft: { open: null, close: null } },
        X: { mor: { open: null, close: null }, aft: { open: null, close: null } },
        J: { mor: { open: null, close: null }, aft: { open: null, close: null } },
        V: { mor: { open: null, close: null }, aft: { open: null, close: null } },
        S: { mor: { open: null, close: null }, aft: { open: null, close: null } },
        D: { mor: { open: null, close: null }, aft: { open: null, close: null } },
      }

      content?.entregas_q_commerce?.horarios
        .sort((a, b) => a.dia - b.dia)
        .map((h) => {
          const wDay = Object.keys(this.qCommSchedule)[h.dia - 1]
          if ((h.inicio || h.fin) && h.type === 'morning') {
            this.qCommSchedule[wDay].mor = { open: h.inicio, close: h.fin }
          } else if ((h.inicio || h.fin) && h.type === 'afternoon') {
            this.qCommSchedule[wDay].aft = { open: h.inicio, close: h.fin }
          }
        })
    },

    // Get transformed data.
    _getTransformedData: function () {
      const formattedSchedule = []
      Object.values(this.qCommSchedule).map((sch, wDay) => {
        formattedSchedule.push({
          dia: wDay + 1,
          inicio: sch.mor.open,
          fin: sch.mor.close,
          type: 'morning',
        })
        formattedSchedule.push({
          dia: wDay + 1,
          inicio: sch.aft.open,
          fin: sch.aft.close,
          type: 'afternoon',
        })
      });

      const formData = new FormData()

      formData.append('_method', 'PUT')
      formData.append('tiene_e_commerce', this.hasECommerce)
      formData.append('tiene_q_commerce', this.hasQCommerce)
      if (this.qCommDelivery)
        formData.append('es_almacen_q_commerce_delivery', this.qCommDelivery)
      if (this.qCommClicknCollect)
        formData.append('es_almacen_q_commerce_click_and_collect', this.qCommClicknCollect)
      if (this.zipCodeID)
        formData.append('codigo_postal_id', this.zipCodeID)
      if (this.address)
        formData.append('direccion', this.address)
      if (this.eCommDays)
        formData.append('dias_e_commerce', this.eCommDays)
      if (this.eCommType)
        formData.append('tipo_recogida_e_commerce_id', this.eCommType)
      if (this.eCommTimetable)
        formData.append('franja_horaria_e_commerce_id', this.eCommTimetable)
      if (this.qCommTimetable)
        formData.append('tiempo_q_commerce', this.qCommTimetable)
      if (formattedSchedule.length)
        formData.append('horarios_q_commerce', JSON.stringify(formattedSchedule))
      formData.append('image', this.image)

      return formData
    }

  }

  return { ...modelState, ...modelActions }
}