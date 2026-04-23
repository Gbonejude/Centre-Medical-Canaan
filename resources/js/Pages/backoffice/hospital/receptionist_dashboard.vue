<template>
  <AdminLayout>
    <div class="content">
      <div class="row">
        <div class="col-sm-12">
          <h4 class="page-title text-primary fw-bold">Gestion des Arrivées & Demandes</h4>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
              <h5 class="card-title mb-0">Demandes de rendez-vous en attente</h5>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Patient</th>
                            <th>Service Demandé</th>
                            <th>Date & Heure</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="req in pending_requests" :key="req.id">
                            <td class="fw-medium">{{ req.patient.name }}</td>
                            <td>{{ req.service.name }}</td>
                            <td>{{ req.appointment_date }} à {{ req.appointment_time }}</td>
                            <td class="text-center">
                                <Link :href="route('backoffice.hospital-appointments.show', req.id)" class="btn btn-primary btn-sm rounded-pill px-3">
                                    Traiter la demande
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="pending_requests.length === 0">
                            <td colspan="4" class="text-center py-5 text-muted">Aucune demande en attente.</td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Médecins actifs</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div v-for="dr in doctors_on_duty" :key="dr.id" class="col-md-4 mb-3">
                            <div class="p-3 border rounded-4 text-center">
                                <div class="avatar-md bg-light-primary text-primary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fa fa-user-md fa-lg"></i>
                                </div>
                                <h6 class="mb-0 fw-bold">Dr. {{ dr.user.name }}</h6>
                                <p class="small text-muted mb-0">{{ dr.service.name }}</p>
                                <span class="badge bg-success-light text-success mt-2">En ligne</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import AdminLayout from "../../components/layouts/AdminLayout.vue";

const props = defineProps({
  pending_requests: Array,
  doctors_on_duty: Array,
});
</script>

<style scoped>
.bg-light-primary { background-color: rgba(0, 123, 255, 0.1); }
.bg-success-light { background-color: rgba(40, 167, 69, 0.1); }
</style>
