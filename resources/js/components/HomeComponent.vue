<template>
    <div>
        <b-row>
            <b-col lg="5" md="12">
                <b-card title="Add Event">
                    <b-card-text>
                        <b-form @submit="save">
                            <b-form-group
                            description="Input event description"
                            label="Event *"
                            label-for="input-1"
                            >
                                <b-form-input id="input-1" :state="Boolean(description)" v-model="description" trim></b-form-input>
                            </b-form-group>
                            <b-form-group>
                                <label class="d-block">Select range *</label>
                                <date-range-picker
                                ref="picker"
                                :opens="opens"
                                :locale-data="{ firstDay: 1, format: 'yyyy-mm-dd HH:MM:ss' }"
                                :minDate="minDate" :maxDate="maxDate"
                                :singleDatePicker="singleDatePicker"
                                :timePicker="timePicker"
                                :timePicker24Hour="timePicker24Hour"
                                :showWeekNumbers="showWeekNumbers"
                                :showDropdowns="showDropdowns"
                                :autoApply="autoApply"
                                v-model="dateRange"
                                :ranges="show_ranges ? undefined : false"
                                @update="updateValues"
                                @toggle="checkOpen"
                                :linkedCalendars="linkedCalendars"
                                :dateFormat="dateFormat"
                                :always-show-calendars="false"
                                :alwaysShowCalendars="alwaysShowCalendars"
                                >
                                <div slot="input" slot-scope="picker">
                                    {{ picker.startDate | date }} - {{ picker.endDate | date }}
                                </div>
                                </date-range-picker>

                                <!-- <button class="btn btn-info" @click="dateRange.startDate = null, dateRange.endDate = null">
                                Clear
                                </button> -->
                            </b-form-group>

                            <b-form-group class="mb-3">
                                <b-form-checkbox-group v-model="selected_days" :state="Boolean(selected_days.length)" size="sm">
                                    <b-form-checkbox value="monday">Mon</b-form-checkbox>
                                    <b-form-checkbox value="tuesday">Tue</b-form-checkbox>
                                    <b-form-checkbox value="wednesday">Wed</b-form-checkbox>
                                    <b-form-checkbox value="thursday">Thu</b-form-checkbox>
                                    <b-form-checkbox value="friday">Fri</b-form-checkbox>
                                    <b-form-checkbox value="saturday">Sat</b-form-checkbox>
                                    <b-form-checkbox value="sunday">Sun</b-form-checkbox>
                                </b-form-checkbox-group>
                            </b-form-group>
                            
                            <b-button variant="success" type="submit">Save 
                                <b-spinner
                                    v-show="submitState"
                                    small
                                    variant="light"
                                ></b-spinner>
                            </b-button>
                        </b-form>
                    </b-card-text>
                </b-card>
            </b-col>
            <b-col lg="7" md="12">
                <b-card title="Event Calendar">
                    <b-card-text>
                        <FullCalendar 
                        ref="fullCalendar"
                        defaultView="listMonth" 
                        :plugins="calendarPlugins"
                        :events="events"
                        allDayText="" />
                    </b-card-text>
                </b-card>
            </b-col>
        </b-row>
    </div>
</template>

<style lang='scss'>

@import '~@fullcalendar/core/main.css';
@import '~@fullcalendar/daygrid/main.css';
@import '~@fullcalendar/list/main.css';

.vue-daterange-picker {
    width: 100%;
    min-width: unset;
}

</style>

<script>
import DateRangePicker from 'vue2-daterange-picker'
//you need to import the CSS manually (in case you want to override it)
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
import moment from 'moment'
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import listPlugin from '@fullcalendar/list'
// ES6 Modules or TypeScript
import Swal from 'sweetalert2'

  export default {
    components: { 
        DateRangePicker,
        FullCalendar 
    },
    filters: {
      date (value) {
        if (!value)
          return ''
        let options = {year: 'numeric', month: 'long', day: 'numeric'};
        return Intl.DateTimeFormat('en-US', options).format(value)
      }
    },
    data() {
        return {
            opens: 'center',
            minDate: '',
            maxDate: '',
            dateRange: {
                startDate: moment().format('YYYY-MM-DD'),
                endDate: moment().format('YYYY-MM-DD'),
            },
            show_ranges: true,
            singleDatePicker: false,
            timePicker: false,
            timePicker24Hour: false,
            showDropdowns: true,
            autoApply: true,
            showWeekNumbers: true,
            linkedCalendars: true,
            alwaysShowCalendars: true,
            calendarPlugins: [ listPlugin ],
            submitState: false,
            selected_days: [],
            description: ''
        }
    },
    methods: {
      checkOpen (open) {
        // console.log('event: open', open)
      },
      dateFormat (classes, date) {
        let yesterday = moment().subtract(1, 'day');
        if (!classes.disabled) {
          classes.disabled = moment(date).isSame(yesterday, 'day')
        }
        return classes
      },
      updateValues (values) {
        this.dateRange.startDate = moment(values.startDate).format('YYYY-MM-DD');
        this.dateRange.endDate = moment(values.endDate).format('YYYY-MM-DD');

        let fullCalendar = this.$refs.fullCalendar.getApi()

        fullCalendar.gotoDate(this.dateRange.startDate)
      },
      events: function(info, successCallback, failureCallback) {
        let start = moment(info.start).format('YYYY-MM-DD')
        let end = moment(info.end).format('YYYY-MM-DD')

        axios.get('/api/event', {
            params: {
                date: start
            }
        }).then(res => {            
            let data = res.data.data

            // If you want an exclusive end date (half-open interval)
            for (let m = moment(start); m.isBefore(end); m.add(1, 'days')) {
                let date = m.format('YYYY-MM-DD')

                let tmp = data.find(elem => {return elem.date === date})

                if(!tmp) data.push({
                    'date' : date,
                    'description': ''
                })
            }
                
            let events = Array.prototype.slice.call( // convert to array
                    data
                ).map(function(event) {                    
                    return {
                        title: event.description,
                        start: event.date
                    }
                })
            
            successCallback(events)

        }).catch(err => {
            failureCallback(err)
        }) 

      },
      save(e) {    
        e.preventDefault()

        if(!this.selected_days.length || !this.description || !this.dateRange.startDate || !this.dateRange.endDate) {

            this.$bvToast.toast('Please fill in required fields (*).', {
            title: `Error`,
            variant: 'danger',
            solid: true
            })
            
            return
        }

        let fullCalendar = this.$refs.fullCalendar.getApi()

        Swal.fire({
            title: 'Are you sure to add event/s?',
            text: 'Some events between the selected dates might be modified or removed.',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            showLoaderOnConfirm: true,
            preConfirm: (login) => {
                return axios.post('/api/event', {
                    date: this.dateRange,
                    days: this.selected_days,
                    description: this.description
                }).then(res => {

                    if(!res.data.length) {
                        throw new Error('No event is added. Selected day/s of the week is not in the selected dates.')
                    }

                    fullCalendar.refetchEvents()

                    return res

                }).catch(err => {

                    Swal.showValidationMessage(
                    `Request failed: ${err}`
                    )

                }).finally(final => {
                    
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value) {
                    Swal.fire({
                        title: 'Event saved!'
                    })
                }
            })
      }
    }
  }
</script>