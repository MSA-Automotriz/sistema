{{-- resources/views/admin/ventas/pos/partials/styles.blade.php --}}
<style>
    .cursor-pointer { cursor: pointer; }
    
    .item-card {
        transition: all 0.3s ease;
        border: 1px solid #e3e6f0;
    }
    
    .item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        border-color: #4e73df;
    }
    
    .popular-item {
        transition: all 0.2s ease;
    }
    
    .popular-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.3rem 0.8rem rgba(0, 0, 0, 0.12) !important;
    }
    
    .item-row {
        transition: all 0.2s ease;
        background: #fff;
    }
    
    .item-row:hover {
        background: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .sticky-sidebar {
        position: sticky;
        top: 20px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
    }
    
    .search-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        border: 1px solid #dee2e6;
    }
    
    .search-input-group {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .badge-stock {
        font-size: 0.7rem;
        padding: 0.25em 0.5em;
    }
    
    .card-hover-effect {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover-effect:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 8px;
    }
    
    .btn-filter {
        border-radius: 20px;
        padding: 0.375rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-filter.active {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border-color: #4e73df;
        color: white;
        box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
    }
    
    .search-results-container {
        min-height: 400px;
        position: relative;
    }

    @media (max-width: 576px) {
        .search-results-container {
            min-height: 300px;
        }
    }
    
    .price-badge {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
        border-radius: 15px;
        padding: 0.375rem 0.75rem;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .category-badge {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        color: white;
        border-radius: 10px;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .toast-container {
        z-index: 1060;
    }
    
    .navbar-search {
        border-radius: 25px;
        border: 2px solid #e3e6f0;
        transition: all 0.3s ease;
    }
    
    .navbar-search:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    /* Tablets y pantallas medianas */
    @media (max-width: 991px) {
        .sticky-sidebar {
            position: relative;
            top: auto;
            max-height: none;
            margin-bottom: 2rem;
        }
    }

    /* Dispositivos móviles */
    @media (max-width: 576px) {
        .btn-filter {
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
        }

        .price-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }

        .item-card {
            margin-bottom: 1rem;
        }

        .badge-stock {
            font-size: 0.6rem;
        }
    }

    /* Pantallas grandes (desktop) */
    @media (min-width: 1200px) {
        .sticky-sidebar {
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }
    }
</style>