<template>
  <vx-card>
    <h4 class="mb-4">User Registration Report</h4>

    <div class="mb-10">
      <label class="m-3">
        <input type="radio" name="category_id" @change="onChange($event)" id="noCheck" value="1" v-model="reportForm.type"> Daily
      </label>

      <label class="m-3">
        <input type="radio" name="category_id"  @change="onChange($event)" id="noCheck" value="2" v-model="reportForm.type"> Weekly
      </label>

      <label class="m-3">
        <input type="radio" name="category_id" @change="onChange($event)" id="noCheck"  value="3" v-model="reportForm.type"> Monthly  
      </label>

      <label class="m-3">
        <input type="radio" name="category_id" @change="onChange($event)" id="noCheck"  value="4" v-model="reportForm.type"> Yearly
      </label>

      <label class="m-3 ">
        <input type="radio" name="category_id" @change="onChange($event)" id="yesCheck"  value="5" v-model="reportForm.type"> Custom 
      </label>

      <label class="m-3">
        <vs-button class="mr-1"  @click="fetchreport()">Get Report</vs-button>
      </label>
    </div>


    <div id="ifYes" style="visibility:hidden">  
      <div class="vx-row">
        <div class="vx-col sm:w-1/3 w-full mb-4" >
          <label class="vs-input--label">Start Date</label>
          <datepicker format="yyyy-MM-dd" placeholder="YYYY-MM-DD" v-model="reportForm.startdate"></datepicker>
        </div>

        <div class="vx-col sm:w-1/3 w-full mb-4" >
          <label class="vs-input--label">End Date</label>
          <datepicker format="yyyy-MM-dd" placeholder="YYYY-MM-DD" v-model="reportForm.enddate"></datepicker>
        </div>
      </div>
    </div>

      
    <div class="mb-8">
      <user-line :chart-data="datacollection" :key="datacollection.length"></user-line>
    </div>


    <div class="mb-8"> 
      <h6 class="m-2 ">User Registration Report Table</h6>
      <vs-table :data="reports">
        <template slot="thead"> 
          <vs-th>Date</vs-th> 
          <vs-th>Total Registration</vs-th> 
          <!-- <vs-th>Agencies</vs-th>  -->
        </template> 
        <template slot-scope="{data}"> 
          <vs-tr :key="index" v-for="(tr, index) in data">
            <vs-td >{{ tr.date}}</vs-td>
            <vs-td>{{ tr.total_users }}</vs-td>
            <!-- <vs-td><a href =''>View Agencies</a></vs-td> -->
          </vs-tr> 
        </template> 
      </vs-table> 
    </div>

  </vx-card>
</template>

<script>

import { required } from 'vuelidate/lib/validators';
import Datepicker from 'vuejs-datepicker'
import axios from 'axios';
import UserLine from './UserLine.vue';

export default {
  name: 'Users-Report',
  components: {
    Datepicker,
    UserLine
  },
  data() {
    return {
      reportForm: {
        startdate: '',
        enddate: '',
        type: '',
      },
      reports:[],
      datacollection: {},
      chartnewdata: {
        date: [],
        user: [],
      },
    }
  },
  validations: {
    reportForm : {
      startdate: { required },
      enddate: { required },
    }
  },
  mounted () {
    this.fetchreport()
  },
  methods: {
    fetchreport(){
      axios.post('/user/registrations', this.reportForm)
      .then((res) => {
        this.reports = JSON.parse(JSON.stringify(res.data.result.data));
        this.chartnewdata.date = [];
        this.chartnewdata.user = [];
        for(let i=0; i<this.reports.length; i++){
          this.chartnewdata.date.push(this.reports[i].date);
          this.chartnewdata.user.push(this.reports[i].total_users);
        }
      });
      setTimeout(() => {
      this.datacollection = {
          labels: JSON.parse(JSON.stringify(this.chartnewdata.date)),
          datasets: [
            {
              label: 'Users',
              fill: false,
              borderColor: '#2554FF',
              backgroundColor: '#2554FF',
              borderWidth: 1,
              tension:0,
              data: JSON.parse(JSON.stringify(this.chartnewdata.user))
            }, 
          ]
        }
      }, 300);
    },
    onChange(event) {
      if (document.getElementById('yesCheck').checked) {
        document.getElementById('ifYes').style.visibility = 'visible';
      }
      else document.getElementById('ifYes').style.visibility = 'hidden';


      var optionText = event.target.value;
      console.log(optionText);
    }
  },
}  
</script>
