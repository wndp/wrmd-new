<template>
  <PublicLayout title="Service Level Agreement (SLA)">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-lg mx-auto">
        <h1>
          <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">Service Level Agreement (SLA)</span>
        </h1>
      </div>
      <div class="mt-6 prose max-w-none prose-blue text-gray-500 mx-auto">
        <h4>Last updated: Oct 10, 2022</h4>
        <h2>Definitions</h2>
        <p>For the purposes of this Service Level Agreement (SLA):</p>
        <ul>
          <li><p><strong>Customer Administrator</strong> means the Administrator within the Customer's organization whose responsibility it will be to receive requests for information and assistance from the Customer and to try to respond to them and be authorized to open and respond to Tickets.</p></li>
          <li><p><strong>Fault</strong> means any repetitive and reproducible problem with the functioning of the Service, always excluding cases in which interruption or degradation to the Service is caused by a deliberate decision by the Service Provider made following behavior of the Customer deemed to be abnormal or incorrect.</p></li>
          <li><p><strong>Service</strong> means the solution that is hosted on the servers and/or cloud that Service Provider designates and that Customer may execute remotely; Service offers various features, including, but not limited to, management of wildlife patient data and storage of constituent's personally identifiable information.</p></li>
          <li><p><strong>Ticket</strong> means the folder, indexed by a unique identification number, created by the Helpdesk management tool when the Customer indicates the existence of a Fault to Support, and in which each intervention by Support is recorded until the Fault is fixed and/or the folder is closed.</p></li>
        </ul>
        <h2>Customer Support</h2>
        <p>The Service Provider offers Customer Support for the Service, by responding to requests for information and assistance from the Customer Administrator in relation to the use of the Service, and fixing Faults in relation to use of the Service that could not be solved by the Customer Administrator, in spite of their best efforts.</p>
        <h3>Access to Support</h3>
        <p>The Customer Administrator may access Support by creating a Ticket, accessible from the customer control panel. Tickets are then available and managed in our internal ticketing tool.  Before opening any Ticket, an initial stage of diagnostics and analysis is to be carried out by the Customer Administrator, particularly including:</p>
        <ul>
          <li>Searching the online documentation and FAQ to check that the solution is not already available,</li>
          <li>Obtaining all useful information from users concerned about the incident encountered and its reproducibility,</li>
          <li>An attempt to reproduce the incident,</li>
          <li>Checking the local network (firewall, proxy, etc.), – which the Service Provider has no means of control</li>
        </ul>
        <h3>How Support Works</h3>
        <p>Support's objective is to restore a normal service operation as quickly as possible and to minimize the impact on business operations, thus ensuring that the best possible levels of service quality and availability are maintained.</p>
        <p>A ticket will be closed and the Customer is informed when: (a) the Fault is resolved; (b) no response is received from the Customer Administrator to a request from Support for additional information required in order to fix the Fault; or, (c) written confirmation from Customer regarding closure of an unresolved ticket.</p>
        <p>Notice will be sent to the Customer Administrator via the Ticket.</p>
        <h2>Service and Credits</h2>
        <h3>Service Availability</h3>
        <p>The Service Provider will make every effort to enable access and use of the Service under the best possible technical conditions. However, the correct functioning of the internet in its entirety cannot be guaranteed and that losses of connection may be due to failures by technical intermediaries (i.e. all third party service providers beyond the control of the Service Provider, but whose service contributes, directly or indirectly, to delivery of the Service).</p>
        <p>In the event of an unexpected interruption to the Service, the Service Provider will take every possible measure to discover the cause and restore the Service in the shortest possible time.</p>
        <p>All unexpected interruptions of service will be notified via our status page.</p>
        <p>The Service Provider guarantees that the Services (meaning any or all of the web facing or API services listed on our status page) will be available 99.99% of the time in any given monthly billing period, excluding maintenance.</p>
        <h3>Planned Maintenance Periods</h3>
        <p>Technical maintenance is defined as maintenance relating to the technical environment (hardware, operating systems, and networks) hosting the Service.</p>
        <p>The Service Provider agrees to take all possible measures in order to limit any inconvenience to the Customer. In general, during maintenance periods, access and use of the Service may be temporarily suspended or degraded.</p>
        <p>For all planned maintenance or improvements that may impact access to the Service, the Service Provider will use its best efforts to notify the Customer with prior notice. In the event of an emergency or critical maintenance operation, the Service Provider reserves the right to intervene without prior notice.</p>
        <h3>Credit Limitations</h3>
        <p>Customer is entitled to a credit of 5% of their given monthly fee for the Service for each thirty (30) minutes of Services unavailability (after the first .01%) in a given monthly period.</p>
        <p>Customer is not entitled to any credit if it is in breach of the Terms of Service until the cure of said breach. Customer is not entitled to any credit if downtime would not have occurred but for its breach of this SLA or Wildlife Rehabilitation MD’s Terms of Service. Customer is not entitled to any credit for downtime or outages resulting from denial of service attacks, virus activity, hacking attempts, or any other circumstances that are not within Wildlife Rehabilitation MD's control.</p>
        <p>In addition, to receive a credit, Customer must make the request by creating a support ticket in the Control Panel within thirty (30) days following the end of the downtime. Customer must show that its use of the Services was adversely affected in some way as a result of the downtime to be eligible for the credit. Notwithstanding anything in these Terms of Service to the contrary, the maximum total credit for the monthly billing period, including all guaranties, shall not exceed 100% of the fees for that billing period. Credits that would be available but for this limitation will not be carried forward to future billing periods.</p>
        <p>Obtaining credits fully discharges Wildlife Rehabilitation MD from any further recourse, these credits being the unique claim Customer may demand in case of non-respect of this SLA.</p>
        <h2>Contact Us</h2>
        <p>If you have any questions about this Service Level Agreement (SLA), You can contact us:</p>
        <ul>
          <li>
            By email: <a href="mailto:support@wildneighborsdp.org?subject=WRMD%20Service%20Level%20Agreement%20(SLA)">support@wildneighborsdp.org</a>
          </li>
          <li>
            By visiting this page on our website: <Link :href="route('contact.create')">
              https://www.wrmd.org/contact
            </Link>
          </li>
        </ul>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>
