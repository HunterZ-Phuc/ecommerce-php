<div class="sidebar-wrapper p-3">
   <div class="card  mb-4  shadow-sm">
      <div class="card-header bg-primary text-white">
         <h5 class="card-title mb-0">
            <i class="fas fa-boxes me-2"></i>Product Categories
         </h5>
      </div>

      <div class="card-body">
         <ul class="sidebar-nav-list">
            <?php getPCates(); ?>
         </ul>
      </div>
   </div>

   <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
         <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Categories
         </h5>
      </div>
      <div class="card-body">
         <ul class="sidebar-nav-list">
            <?php getCat(); ?>
         </ul>
      </div>
   </div>
   </div>

   <style>
      .sidebar-wrapper {
         background-color: #f8f9fa;
         border-radius: 8px;
      }

      .sidebar-wrapper .card {
         border: none;
         transition: transform 0.2s;
      }

      .sidebar-wrapper .card:hover {
         transform: translateY(-2px);
      }

      .sidebar-wrapper .card-header {
         border-radius: 6px 6px 0 0 !important;
      }

      .sidebar-nav-list {
         list-style: none;
         padding: 0;
         margin: 0;
      }

      .sidebar-nav-list li {
         padding: 12px 15px;
         margin: 2px 0;
         border-radius: 4px;
         transition: all 0.2s;
         background-color: transparent;
      }

      .sidebar-nav-list a {
         text-decoration: none;
         font-family: 'Arial', sans-serif;
      }

      .sidebar-nav-list li:hover {
         background-color: #e9ecef;
         padding-left: 20px;
         color: #0056b3;
         font-weight: bold;
      }
   </style>