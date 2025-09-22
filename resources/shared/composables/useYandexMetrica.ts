import { ref, onUnmounted, readonly } from 'vue'

/**
 * Composable для динамического подключения Яндекс.Метрики
 * Подключает или отключает счетчик в зависимости от наличия кода метрики
 */
export function useYandexMetrica() {
  const metricaCode = ref<string | null>(null)
  const isLoaded = ref(false)
  
  /**
   * Инициализация Яндекс.Метрики с заданным кодом
   */
  const initMetrica = (code: string | null) => {
    if (!code || typeof window === 'undefined') {
      return
    }
    
    try {
      // Удаляем предыдущий скрипт, если есть
      removeMetricaScript()
      
      // Создаем новый скрипт
      const script = document.createElement('script')
      script.type = 'text/javascript'
      script.async = true
      script.innerHTML = `
        (function(m,e,t,r,i,k,a){
          m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
          m[i].l=1*new Date();
          k=e.createElement(t),a=e.getElementsByTagName(t)[0],
          k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
        })(window, document, "script", "https://mc.yandex.ru/metrika/watch.js", "ym");
        
        ym(${code}, "init", {
          clickmap:true,
          trackLinks:true,
          accurateTrackBounce:true,
          webvisor:true
        });
      `
      
      // Добавляем script в head
      document.head.appendChild(script)
      
      // Создаем noscript для случаев без JavaScript
      const noscript = document.createElement('noscript')
      noscript.innerHTML = `
        <div>
          <img src="https://mc.yandex.ru/watch/${code}" style="position:absolute; left:-9999px;" alt="" />
        </div>
      `
      document.head.appendChild(noscript)
      
      metricaCode.value = code
      isLoaded.value = true
      
      console.log(`✅ Яндекс.Метрика инициализирована с кодом: ${code}`)
    } catch (error) {
      console.error('❌ Ошибка инициализации Яндекс.Метрики:', error)
      isLoaded.value = false
    }
  }
  
  /**
   * Удаление скрипта Яндекс.Метрики
   */
  const removeMetricaScript = () => {
    if (typeof window === 'undefined') {
      return
    }
    
    // Удаляем существующие скрипты метрики
    const existingScripts = document.querySelectorAll('script[src*="mc.yandex.ru"]')
    existingScripts.forEach(script => script.remove())
    
    // Удаляем inline скрипты с метрикой
    const inlineScripts = document.querySelectorAll('script')
    inlineScripts.forEach(script => {
      if (script.innerHTML.includes('mc.yandex.ru/metrika')) {
        script.remove()
      }
    })
    
    // Удаляем noscript элементы
    const noscriptElements = document.querySelectorAll('noscript')
    noscriptElements.forEach(noscript => {
      if (noscript.innerHTML.includes('mc.yandex.ru/watch')) {
        noscript.remove()
      }
    })
    
    // Очищаем глобальный объект ym, если он существует
    if (window.ym) {
      try {
        delete window.ym
      } catch (error) {
        // В некоторых браузерах delete может не работать
        window.ym = undefined
      }
    }
    
    isLoaded.value = false
    console.log('🗑️ Яндекс.Метрика удалена')
  }
  
  /**
   * Отправка цели в Яндекс.Метрику
   */
  const reachGoal = (goal: string, params?: Record<string, any>) => {
    if (!isLoaded.value || !metricaCode.value || typeof window === 'undefined') {
      console.warn('⚠️ Яндекс.Метрика не загружена, цель не отправлена:', goal)
      return
    }
    
    try {
      if (window.ym) {
        window.ym(metricaCode.value, 'reachGoal', goal, params)
        console.log(`🎯 Цель отправлена в Яндекс.Метрику: ${goal}`, params)
      }
    } catch (error) {
      console.error('❌ Ошибка отправки цели в Яндекс.Метрику:', error)
    }
  }
  
  /**
   * Отправка события в Яндекс.Метрику
   */
  const hit = (url?: string, options?: Record<string, any>) => {
    if (!isLoaded.value || !metricaCode.value || typeof window === 'undefined') {
      return
    }
    
    try {
      if (window.ym) {
        window.ym(metricaCode.value, 'hit', url, options)
      }
    } catch (error) {
      console.error('❌ Ошибка отправки hit в Яндекс.Метрику:', error)
    }
  }
  
  // Очистка при размонтировании компонента
  onUnmounted(() => {
    removeMetricaScript()
  })
  
  return {
    metricaCode: readonly(metricaCode),
    isLoaded: readonly(isLoaded),
    initMetrica,
    removeMetricaScript,
    reachGoal,
    hit
  }
}

// Типизация для глобального объекта ym
declare global {
  interface Window {
    ym?: (counterId: string | number, method: string, ...args: any[]) => void
  }
} 