<template>
  <header class="app-header">
    <div class="app-header__container">
      <!-- Logo -->
      <div class="app-header__logo">
        <MdiIcon name="silverware-fork-knife" size="lg" color="primary" />
        <span class="app-header__title">Restaurant Dashboard</span>
      </div>
      
      <!-- Navigation -->
      <nav class="app-header__nav">
        <button class="app-header__nav-item app-header__nav-item--active">
          <MdiIcon name="view-dashboard" size="sm" />
          <span>Дашборд</span>
        </button>
        
        <button class="app-header__nav-item">
          <MdiIcon name="food" size="sm" />
          <span>Меню</span>
        </button>
        
        <button class="app-header__nav-item">
          <MdiIcon name="calendar" size="sm" />
          <span>События</span>
        </button>
        
        <button class="app-header__nav-item">
          <MdiIcon name="newspaper" size="sm" />
          <span>Новости</span>
        </button>
      </nav>
      
      <!-- User menu -->
      <div class="app-header__user">
        <button class="app-header__notifications">
          <MdiIcon name="bell" size="md" />
          <span class="app-header__badge">3</span>
        </button>
        
        <div class="app-header__profile">
          <MdiIcon name="account-circle" size="lg" color="gray" />
          <span class="app-header__username">Админ</span>
          <MdiIcon name="chevron-down" size="sm" color="gray" />
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { MdiIcon } from '@shared/ui'
</script>

<style>
/* App Header Component - PostCSS Nested */
.app-header {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  padding: 0 24px;

  &__container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 64px;
    max-width: 1200px;
    margin: 0 auto;
  }

  &__logo {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  &__title {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
  }

  &__nav {
    display: flex;
    gap: 24px;
  }

  &__nav-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border: none;
    background: none;
    color: #6b7280;
    font-size: 14px;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.2s;

    &:hover {
      background: #f3f4f6;
      color: #1f2937;
    }

    &--active {
      background: #eff6ff;
      color: #3b82f6;
    }
  }

  &__user {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  &__notifications {
    position: relative;
    padding: 8px;
    border: none;
    background: none;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.2s;

    &:hover {
      background: #f3f4f6;
    }
  }

  &__badge {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 16px;
    text-align: center;
  }

  &__profile {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.2s;

    &:hover {
      background: #f3f4f6;
    }
  }

  &__username {
    font-size: 14px;
    font-weight: 500;
    color: #1f2937;
  }
}
</style> 