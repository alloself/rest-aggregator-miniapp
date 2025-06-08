<template>
  <AppCard class="news-card">
    <div v-if="newsItem.image" class="news-card__image-container">
      <AppImage
        :src="newsItem.image"
        :alt="newsItem.title"
        width="100%"
        height="160"
        object-fit="cover"
        border-radius="lg"
      />
    </div>
    
    <div class="news-card__content">
      <h4 class="news-card__title">{{ newsItem.title }}</h4>
      <p class="news-card__excerpt">{{ newsItem.excerpt }}</p>
      
      <div class="news-card__meta">
        <span class="news-card__date">{{ formatDate(newsItem.date) }}</span>
        <Tag 
          :value="getCategoryLabel(newsItem.category)"
          severity="info"
          size="small"
        />
      </div>
    </div>
  </AppCard>
</template>

<script setup lang="ts">
import { AppCard, AppImage } from '../../shared/ui'
import Tag from 'primevue/tag'

interface NewsItem {
  id: string
  title: string
  excerpt: string
  image?: string
  date: string
  category: string
}

interface NewsCardProps {
  newsItem: NewsItem
}

defineProps<NewsCardProps>()

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('ru-RU', {
    day: 'numeric',
    month: 'long'
  })
}

const getCategoryLabel = (category: string) => {
  const labels = {
    news: 'Новости',
    events: 'События', 
    offers: 'Предложения'
  }
  return labels[category as keyof typeof labels] || category
}
</script>

<style>
/* News Card Component - PostCSS Nested */
.news-card {
  background-color: var(--color-white);
  border-radius: var(--border-radius-xl);
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition: all var(--transition-base);

  &:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
  }

  &__image-container {
    width: 100%;
    height: 160px;
    overflow: hidden;
  }

  &__content {
    padding: var(--spacing-lg);
  }

  &__title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    margin-bottom: var(--spacing-sm);
    line-height: 1.3;
    color: var(--color-text-primary);
  }

  &__excerpt {
    font-size: var(--font-size-sm);
    color: var(--color-text-secondary);
    line-height: 1.4;
    margin-bottom: var(--spacing-md);
  }

  &__meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: var(--font-size-xs);
    color: var(--color-text-secondary);
  }

  &__date {
    font-weight: var(--font-weight-medium);
  }
}
</style> 