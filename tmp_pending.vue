<template>
  <Head>
    <title>Pending Payments - {{ stats.pending_count }} customers</title>
    <meta name="description" content="List of customers with pending payments" />
  </Head>

  <div class="content-wrapper">
    <div class="container">
      
      <div class="page-header">
        <div class="header-content">
          <div class="header-title">
            <div class="title-icon">
              <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="title-text">
              <h1>Pending Payments</h1>
              <p>{{ stats.pending_count }} customers with outstanding balances</p>
            </div>
          </div>
          <div class="header-actions">
            <Link :href="route('dashboard.index')" class="btn btn-outline-primary">
              <i class="fas fa-home"></i>
              Back to Dashboard
            </Link>
          </div>
        </div>
      </div>

      
      <div class="stats-grid">
        <div class="stat-card total">
          <div class="stat-icon">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-info">
            <h3>{{ pendingPayments.total }}</h3>
            <p>Total Customers</p>
          </div>
        </div>

        <div class="stat-card home-care">
          <div class="stat-icon">
            <i class="fas fa-home"></i>
          </div>
          <div class="stat-info">
            <h3>{{ getStatsByType('home_care_clients') }}</h3>
            <p>Home Care</p>
          </div>
        </div>

        <div class="stat-card residential">
          <div class="stat-icon">
            <i class="fas fa-building"></i>
          </div>
          <div class="stat-info">
            <h3>{{ getStatsByType('residential_clients') }}</h3>
            <p>Residential</p>
          </div>
        </div>

        <div class="stat-card private">
          <div class="stat-icon">
            <i class="fas fa-user-tie"></i>
          </div>
          <div class="stat-info">
            <h3>{{ getStatsByType('private_clients') }}</h3>
            <p>Private</p>
          </div>
        </div>
      </div>

      
      <div class="filters-section">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input
            type="text"
            placeholder="Search customers..."
            v-model="searchQuery"
            class="search-input"
          />
        </div>
        <div class="filter-controls">
          <select v-model="typeFilter" class="filter-select">
            <option value="">All Types</option>
            <option value="home_care_clients">Home Care</option>
            <option value="residential_clients">Residential</option>
            <option value="private_clients">Private</option>
          </select>
          <select v-model="sortBy" class="filter-select">
            <option value="remaining_amount">Highest Amount</option>
            <option value="due_date">Oldest Due Date</option>
            <option value="customer_name">Customer Name</option>
            <option value="days_overdue">Most Overdue</option>
          </select>
          <button @click="resetFilters" class="btn btn-secondary">
            <i class="fas fa-refresh"></i>
            Reset
          </button>
        </div>
      </div>

      
      <div v-if="hasActiveFilters" class="filter-summary">
        <div class="alert alert-info">
          <i class="fas fa-filter"></i>
          <span>
            Showing {{ filteredPayments.length }} of {{ pendingPayments.data.length }} customers
            <span v-if="searchQuery"> · Search: "{{ searchQuery }}"</span>
            <span v-if="typeFilter"> · Type: {{ formatCustomerType(typeFilter) }}</span>
          </span>
          <button @click="resetFilters" class="btn btn-sm btn-outline-secondary">
            Clear filters
          </button>
        </div>
      </div>

      
      <div class="table-container">
        <div class="table-header">
          <h2>
            <i class="fas fa-list"></i>
            Pending Payments List
          </h2>
         
        </div>

        <div class="table-wrapper">
          <table class="payments-table">
            <thead>
              <tr>
                <th>Type</th>
                <th>Customer</th>
                <th>Period</th>
                <th>Amount Due</th>
                <th>Paid</th>
                <th>Remaining</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="payment in filteredPayments" :key="`${payment.customer_id}-${payment.month}-${payment.year}`">
                <td>
                  <span :class="[
                    'badge',
                    'customer-type-badge',
                    getCustomerTypeClass(payment.type),
                  ]">
                    {{ formatCustomerType(payment.type) }}
                  </span>
                </td>
                <td>
                  <div class="customer-info">
                    <div class="customer-avatar" :style="getAvatarStyle(payment.type)">
                      {{ getInitials(payment.customer_name) }}
                    </div>
                    <div class="customer-details">
                      <div class="customer-name">{{ payment.customer_name }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="period-badge">
                    <i class="fas fa-calendar"></i>
                    {{ formatPeriod(payment.month, payment.year) }}
                  </div>
                </td>
                <td>
                  <div class="amount-display">
                    <span class="amount">{{ formatCurrency(payment.initial_amount_due) }}</span>
                  </div>
                </td>
                <td>
                  <div class="amount-display">
                    <span class="amount paid">{{ formatCurrency(payment.total_paid) }}</span>
                    <div class="progress-bar">
                      <div class="progress-fill" :style="{ width: getProgressWidth(payment) }"></div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="amount-display">
                    <span class="amount remaining">{{ formatCurrency(payment.remaining_amount) }}</span>
                  </div>
                </td>
                <td>
                  <span class="status-badge" :class="getStatusClass(payment.days_overdue)">
                    <i :class="getStatusIcon(payment.days_overdue)"></i>
                    {{ getStatusText(payment.days_overdue) }}
                  </span>
                </td>
                <td>
                  <div class="action-buttons">
                    <button
                      class="btn btn-sm btn-primary"
                      @click="viewCustomerDetails(payment)"
                      title="View Details"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                    <button
                      class="btn btn-sm btn-success"
                      @click="addPayment(payment)"
                      title="Add Payment"
                    >
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        
        <div class="pagination-wrapper" v-if="pendingPayments.last_page > 1">
          <div class="pagination-info">
            Showing {{ pendingPayments.from }} to {{ pendingPayments.to }} of {{ pendingPayments.total }} results
          </div>
          <div class="pagination-controls">
            <Link
              v-for="link in pendingPayments.links"
              :key="link.label"
              :href="link.url"
              class="pagination-link"
              :class="{ active: link.active, disabled: !link.url }"
              v-html="link.label"
            ></Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";

const toast = useToast();

const props = defineProps({
  pendingPayments: Object,
  stats: Object
});

const CUSTOMER_TYPES = {
  HOME_CARE_CLIENTS: 'home_care_clients',
  RESIDENTIAL_CLIENTS: 'residential_clients',
  PRIVATE_CLIENTS: 'private_clients'
};

const searchQuery = ref('');
const typeFilter = ref('');
const sortBy = ref('remaining_amount');

const hasActiveFilters = computed(() => {
  return searchQuery.value || typeFilter.value;
});

const filteredPayments = computed(() => {
  let filtered = [...props.pendingPayments.data];

  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(payment =>
      payment.customer_name.toLowerCase().includes(query) ||
      payment.email?.toLowerCase().includes(query) ||
      payment.phone?.includes(query)
    );
  }

  
  if (typeFilter.value) {
    filtered = filtered.filter(payment => payment.type === typeFilter.value);
  }

  
  filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'remaining_amount':
        return b.remaining_amount - a.remaining_amount;
      case 'due_date':
        return new Date(a.due_date) - new Date(b.due_date);
      case 'customer_name':
        return a.customer_name.localeCompare(b.customer_name);
      case 'days_overdue':
        return b.days_overdue - a.days_overdue;
      default:
        return 0;
    }
  });

  return filtered;
});

const resetFilters = () => {
  searchQuery.value = "";
  typeFilter.value = "";
};

const getStatsByType = (type) => {
  return props.pendingPayments.data.filter(payment => payment.type === type).length;
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount || 0);
};

const formatPeriod = (month, year) => {
  const date = new Date(year, month - 1);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    year: 'numeric'
  });
};

const getProgressWidth = (payment) => {
  const percentage = (payment.total_paid / payment.initial_amount_due) * 100;
  return `${Math.min(percentage, 100)}%`;
};

const getStatusClass = (daysOverdue) => {
  if (daysOverdue > 30) return 'status-critical';
  if (daysOverdue > 7) return 'status-warning';
  if (daysOverdue > 0) return 'status-overdue';
  return 'status-upcoming';
};

const getStatusIcon = (daysOverdue) => {
  if (daysOverdue > 30) return 'fas fa-exclamation-triangle';
  if (daysOverdue > 7) return 'fas fa-exclamation-circle';
  if (daysOverdue > 0) return 'fas fa-clock';
  return 'fas fa-calendar-check';
};

const getStatusText = (daysOverdue) => {
  if (daysOverdue > 30) return 'Critical';
  if (daysOverdue > 7) return 'Overdue';
  if (daysOverdue > 0) return 'Late';
  return 'Due Soon';
};

const getCustomerTypeClass = (type) => {
  switch (type) {
    case CUSTOMER_TYPES.HOME_CARE_CLIENTS:
      return 'type-home-care';
    case CUSTOMER_TYPES.RESIDENTIAL_CLIENTS:
      return 'type-residential';
    case CUSTOMER_TYPES.PRIVATE_CLIENTS:
      return 'type-private';
    default:
      return 'type-default';
  }
};

const getAvatarStyle = (type) => {
  switch (type) {
    case CUSTOMER_TYPES.HOME_CARE_CLIENTS:
      return 'background-color: #dc3545; color: white;';
    case CUSTOMER_TYPES.RESIDENTIAL_CLIENTS:
      return 'background-color: #28a745; color: white;';
    case CUSTOMER_TYPES.PRIVATE_CLIENTS:
      return 'background-color: #17a2b8; color: white;';
    default:
      return 'background-color: #6c757d; color: white;';
  }
};

const formatCustomerType = (type) => {
  if (!type) return '';

  switch (type) {
    case CUSTOMER_TYPES.HOME_CARE_CLIENTS:
      return 'Home Care';
    case CUSTOMER_TYPES.RESIDENTIAL_CLIENTS:
      return 'Residential';
    case CUSTOMER_TYPES.PRIVATE_CLIENTS:
      return 'Private';
    default:
      return type.replace(/_/g, ' ').replace(/\w\S*/g,
        (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()
      );
  }
};

const getInitials = (name) => {
  if (!name) return "";
  return name
    .split(" ")
    .map((part) => part.charAt(0))
    .join("")
    .toUpperCase()
    .substring(0, 2);
};

const viewCustomerDetails = (payment) => {
  router.get(route('customers.show', payment.customer_uuid));
};

const addPayment = (payment) => {
  router.visit(route('customers.payments.createForCustomer', payment.customer_uuid), {
    method: 'post',
    data: {
      remaining_amount: payment.remaining_amount,
      month: payment.month,
      year: payment.year
    }
  });
};

</script>

<style scoped>
.content-wrapper {
  padding: 2rem;
  background: #f8f9fa;
  min-height: 100vh;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-title {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.title-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #ff6b35, #ff4500);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.title-text h1 {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
  color: #333;
}

.title-text p {
  margin: 0.5rem 0 0 0;
  color: #666;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-card .stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
}

.stat-card.total .stat-icon {
  background: linear-gradient(135deg, #007bff, #0056b3);
}

.stat-card.home-care .stat-icon {
  background: linear-gradient(135deg, #dc3545, #c82333);
}

.stat-card.residential .stat-icon {
  background: linear-gradient(135deg, #28a745, #1e7e34);
}

.stat-card.private .stat-icon {
  background: linear-gradient(135deg, #17a2b8, #138496);
}

.stat-info h3 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #333;
}

.stat-info p {
  margin: 0.25rem 0 0 0;
  color: #666;
  font-size: 0.9rem;
}

.filters-section {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  align-items: center;
  flex-wrap: wrap;
}

.search-box {
  position: relative;
  flex: 1;
  min-width: 300px;
}

.search-box i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #007bff;
}

.filter-controls {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.filter-select {
  padding: 0.75rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  background: white;
  cursor: pointer;
  min-width: 150px;
}

.filter-select:focus {
  outline: none;
  border-color: #007bff;
}

.filter-summary {
  margin-bottom: 2rem;
}

.filter-summary .alert {
