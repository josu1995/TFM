const UpdateStore = () => {
    let debounce = null
    let storeMarker = null
    const gMap = new google.maps.Map(document.getElementById('gMap'), {
      center: { lat: 39.3705675, lng: -3.4201541 },
      zoom: 5,
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
      latitud:null,
      longitud:null,
      hasECommerce: false,
      eCommType: null,
      id:null,
      eCommDays: ['true'],
      eCommTimetable: 4,
      hasQCommerce: false,
      qCommClicknCollect: false,
      qCommDelivery: false,
      qCommTimetable: 15,
      qCollTimetable:15,
      eTprep: 240,
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
      openModalButton: function(){
        
        this._initializeStoreData(null);
      },
      openModal: function (content) {

        this.$nextTick(() => this.$refs.EditStoreModal.focus())
       
        $('body').addClass('modal-open')
  
        this._initializeStoreData(content)
  
        this.handleAddressSearch()
        
        setTimeout(() => { this.modalOpened = true }, 200)
      },
      abrir: function(id){
        
        $('#'+id+'-mor1').show();
      
        if(this.qCommSchedule[id].mor.open == null){
          this.qCommSchedule[id].mor.open = '00:00'
        }

        if(this.qCommSchedule[id].mor.close == null){
          this.qCommSchedule[id].mor.close = '00:00';
        }
        

      },
      cerrar: function(id){
        
        $('#'+id+'-mor1').hide();
        $('#'+id+'-mor1-text').text('Cerrado');
        this.qCommSchedule[id].mor = { open: null, close: null }
        
      },

      abrir1: function(id){
        
        $('#'+id+'-aft1').show();

        if(this.qCommSchedule[id].aft.open == null){
          this.qCommSchedule[id].aft.open = '00:00'
        }

        if(this.qCommSchedule[id].aft.close == null){
          this.qCommSchedule[id].aft.close = '00:00';
        }
        
      },
      cerrar1: function(id){
        
        $('#'+id+'-aft1').hide();

        $('#'+id+'-aft1-text').text('Cerrado');

        this.qCommSchedule[id].aft = { open: null, close: null }
    
      },
     
      // Close edit modal.
      closeModal: function (event) {
        if (!this.modalOpened) return
        
        $('body').removeClass('modal-open')
        this.modalOpened = false
        $('#alertas').hide();
        setTimeout(() => { this.store = null }, 200)
      },
  
      // Handle zipcode search.
      findZipCode: function async(v) {
        
        if (debounce) clearTimeout(debounce)
        
        debounce = setTimeout(async () => {
         
          if(v == 1){
            var response= await fetch(`/codigos-postales/search?t=${this.zipCode}&p=64`)
          }else{
            var response = await fetch(`/codigos-postales/search?t=&p=64`)
          }
          
          this.zipResults = await response.json()
        }, 300)
      },

      prueba: function(){
        
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
            this.latitud = null
            this.longitud = null
            return
          }
  
          if (storeMarker && (storeMarker.position.lat() !== results[0].geometry.location.lat() || storeMarker.position.lng() !== results[0].geometry.location.lng())) {
            
            storeMarker.setMap(null)
            storeMarker = null
          }
  
          //if (!storeMarker || (storeMarker && (storeMarker.position.lat() !== results[0].geometry.location.lat() || storeMarker.position.lng() !== results[0].geometry.location.lng()))) {
           
            storeMarker = new google.maps.Marker({
              map: gMap,
              position: results[0].geometry.location,
              icon: '/img/maps/transporter-business-marker.png'
            })

            this.latitud = storeMarker.position.lat();
            this.longitud = storeMarker.position.lng();
            gMap.panTo({lat:storeMarker.position.lat(),lng: storeMarker.position.lng() - 0.001})
            gMap.setZoom(16)
         // }
         

          
        }
        const addressObj = { address: `${this.address}, ${this.zipCode.split('-')[0].trim()}, ${this.zipCode.split('-')[1].trim()}, ES` }
        new google.maps.Geocoder().geocode(addressObj, handleGeoCoderRequest)
      },

      changeAddressSearch: function (event) {
       
        const handleGeoCoderRequest = (results, status) => {
          if (status != google.maps.GeocoderStatus.OK || results[0].types[0] === 'postal_code' || results[0].types[0] === 'locality') {
            
            if (storeMarker) 
            
            
            this.latitud = null
            this.longitud = null
            return


          }
  
          if (storeMarker && (storeMarker.position.lat() !== results[0].geometry.location.lat() || storeMarker.position.lng() !== results[0].geometry.location.lng())) {
            
          }
          //if (!storeMarker || (storeMarker && (storeMarker.position.lat() !== results[0].geometry.location.lat() || storeMarker.position.lng() !== results[0].geometry.location.lng()))) {
           
            storeMarker = new google.maps.Marker({
              map: gMap,
              position: {lat:storeMarker.position.lat(),lng: storeMarker.position.lng()},
              icon: '/img/maps/transporter-business-marker.png'
            })
            this.latitud = storeMarker.position.lat();
            this.longitud = storeMarker.position.lng();
         // }
         

          
        }

        const addressObj = { address: `${this.address}, ${this.zipCode.split('-')[0].trim()}, ${this.zipCode.split('-')[1].trim()}, ES` }
        new google.maps.Geocoder().geocode(addressObj, handleGeoCoderRequest)
      },

      changeAddressSearchKey: function (e) {


        const handleGeoCoderRequest = (results, status) => {
          if (status != google.maps.GeocoderStatus.OK || results[0].types[0] === 'postal_code' || results[0].types[0] === 'locality') {
            
            if (storeMarker) 
            
            
            this.latitud = null
            this.longitud = null
            return


          }
  
          if (storeMarker && (storeMarker.position.lat() !== results[0].geometry.location.lat() || storeMarker.position.lng() !== results[0].geometry.location.lng())) {
            
          }
          //if (!storeMarker || (storeMarker && (storeMarker.position.lat() !== results[0].geometry.location.lat() || storeMarker.position.lng() !== results[0].geometry.location.lng()))) {
           
            storeMarker = new google.maps.Marker({
              map: gMap,
              position: {lat:storeMarker.position.lat(),lng: storeMarker.position.lng()},
              icon: '/img/maps/transporter-business-marker.png'
            })
            this.latitud = storeMarker.position.lat();
            this.longitud = storeMarker.position.lng();
         // }
         

          
        }

        if (e.keyCode == 8 || e.keyCode == 46) { 
          const addressObj = { address: `${this.address}, ${this.zipCode.split('-')[0].trim()}, ${this.zipCode.split('-')[1].trim()}, ES` }
          new google.maps.Geocoder().geocode(addressObj, handleGeoCoderRequest)
        }
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
        var id = 0;
        if(this.store != null){
          id = this.store.id;
        }

          this.loading = true
          this.errors = []
          
          let response = null
          try {
              response = await fetch(`/api/tbusiness/v1/configuracion/stores/${id}`, {
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
        $('#cp').focusin(function(){
          
          
        });

        
        $('#cp').focusout(function(){
          //$('.AutocompleteResults').hide();
          
          
        });
      
        $('#cp').on('input', function() {
          
          setTimeout(showAuto, 2000);
        });

        function showAuto() {
          //$('.AutocompleteResults').show();
        }

          $('#cp').on('change',function(){
            //this.zipCode = '';

            $('#cp').val('');
            
            $('#direccion').val(''); 
            if(storeMarker!=null){
                    
              storeMarker.setMap(null);
                  
              storeMarker = null;               
            }
            
            gMap.panTo({lat: 39.3705675, lng:-3.4201541});
            gMap.setZoom(5);
        });
        if(content != null){
          $('#crearEditarModal').text('Editar almacén');
          $('#crearEditar').text('Editar');
          
        this.store = content
        
        this.selectZipCode(content?.codigo_postal)
        this.address = content?.direccion
        $('#direccion').val( this.address);
        
        this.image = this.imagePreview = content?.entregas_q_commerce?.imagen
    
        this.hasECommerce = !!content?.entregas_e_commerce
        this.eCommType = content?.entregas_e_commerce?.tipo_recogida_e_commerce_id

        fiestas = [];  
        for(i = 0; i < content?.festivos.length;i++){
        
          fiestas.push(content?.festivos[i]?.festivo);
        }
        
        $('#multipleCalendar').datepicker({
          multidate: true
            
        });

        if(fiestas.length > 0){
          $( '#multipleCalendar' ).datepicker("setDate", fiestas);
        }else{
          $( '#multipleCalendar' ).datepicker("setDate", '');
        }
        

        if(!this.eCommType){
          
          this.eCommType = 1;

        }
       
        this.eCommDays = content?.entregas_e_commerce?.dias?.split(',')
        if(this.eCommDays){
          
        }else{
          
          this.eCommDays = ['true'];
        }

        this.eCommTimetable = content?.entregas_e_commerce?.franja_horaria_id

        if(this.eCommTimetable){

        }else{
          this.eCommTimetable = 4;
        }
          
        this.hasQCommerce = !!content?.entregas_q_commerce
        this.qCommClicknCollect = !!content?.entregas_q_commerce?.es_almacen_click_and_collect
        this.qCommDelivery = !!content?.entregas_q_commerce?.es_almacen_delivery
        this.qCommTimetable = content?.entregas_q_commerce?.tiempo
        this.qCollTimetable = content?.entregas_q_commerce?.tiempo_collect
        this.eTprep = content?.entregas_e_commerce?.tprep
        if(this.qCommTimetable){
          
        }else{
          this.qCommTimetable = 15;
        }

        if(this.qCollTimetable){
         
        }else{
          if( content?.entregas_q_commerce?.tiempo_collect == 0){
            this.qCollTimetable = 0;
          }else{
            this.qCollTimetable = 15;
          }
          
        }

        if(this.eTprep){

        }else{
          
          this.eTprep = 240;
        }

        if(storeMarker){
          
            
          storeMarker.setMap(null);

          gMap.panTo({lat:storeMarker.position.lat(),lng: storeMarker.position.lng() - 0.001})
          gMap.setZoom(16)
          
        }
        
       //
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
        }else{
          
          $('#crearEditarModal').text('Crear almacén');
          $('#crearEditar').text('Crear');
          if(storeMarker!=null){
            
            storeMarker.setMap(null);
          }
          
          gMap.panTo({lat: 39.3705675, lng:-3.4201541});
          gMap.setZoom(5);
          this.map= null,
          this.store= null,
          this.modalOpened= false,
          this.loading= false,
          this.errors= [],
      
          this.zipCode= null,
          this.zipCodeID= null,
          this.address= null,
          this.latitud = null,
          this.longitud = null,
          this.hasECommerce= false,
          this.eCommType=1,
          this.id = null,
          this.eCommDays= ['true'],
          this.eCommTimetable= 4,
          this.hasQCommerce= false,
          this.qCommClicknCollect= false,
          this.qCommDelivery= false,
          this.qCommTimetable= 15,
          this.qCollTimetable = 15,
          this.eTprep = 240,
          this.showSchedulerFor= null,
          this.qCommSchedule= { ...defaultSchedule },
          this.image= null,
          this.imagePreview= null,
      
          this.zipResults= []
        }
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
        if(this.latitud)
          formData.append('latitud',this.latitud)
        if(this.longitud)
          formData.append('longitud',this.longitud)
        if (this.eCommDays)
          formData.append('dias_e_commerce', this.eCommDays)
        if (this.eCommType)
          formData.append('tipo_recogida_e_commerce_id', this.eCommType)
        if (this.eCommTimetable)
          formData.append('franja_horaria_e_commerce_id', this.eCommTimetable)
        if (this.qCommTimetable)
          formData.append('tiempo_q_commerce', this.qCommTimetable)
        if(this.qCollTimetable)
          formData.append('tiempo_collect', this.qCollTimetable)
          if(this.eTprep)
          formData.append('tprep', this.eTprep)
        if (formattedSchedule.length)
          formData.append('horarios_q_commerce', JSON.stringify(formattedSchedule))
        formData.append('image', this.image)
        formData.append('festivos',$('#multipleCalendar').val())
        return formData
      }
  
    }
  
    return { ...modelState, ...modelActions }
  }