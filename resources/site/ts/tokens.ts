/**
 * Design Tokens для Mini App Restaurant Aggregator
 * Основано на анализе Figma дизайна Mini App
 * Совместимо с Tailwind CSS 4 + PrimeVue
 */

export const designTokens = {
  // Основные цвета
  colors: {
    // Основной фон
    background: {
      primary: '#FFFEFB',        // fill_CKYBBK - основной фон приложения
      secondary: '#F4F3F3',      // fill_6AOEJP - фон для фото контейнеров
      card: '#FFFEFD',           // fill_LXFTDP - фон карточек
      overlay: 'rgba(235, 235, 235, 0.38)', // fill_LXG6D8 - полупрозрачный overlay
      disabled: 'rgba(0, 0, 0, 0.2)', // fill_PV2D36 - фон отключенных элементов
      section: '#BDBDBD',        // fill_WR6U3B - фон секций
    },

    // Текстовые цвета
    text: {
      primary: '#000000',        // fill_ECJC70 - основной черный текст
      secondary: 'rgba(0, 0, 0, 0.35)', // fill_83X0TS - вторичный серый текст
      accent: '#1C1C1E',         // fill_IPG3TJ - акцентный темный текст
      muted: 'rgba(0, 0, 0, 0.4)', // fill_639QP7 - приглушенный текст
      dark: '#050505',           // fill_Q4Q0HR - самый темный текст
    },

    // Белый цвет и его вариации
    white: {
      pure: '#FFFFFF',           // fill_IFRPVC - чистый белый
      soft: '#F5F5F5',           // fill_3O1N6O - мягкий белый для неактивных элементов
    },

    // Цвета интерфейса
    surface: {
      default: '#EEEEEE',        // fill_RLS316 - стандартные кнопки и поверхности
      light: '#F2F2F1',          // fill_S82JE9 - светлые поверхности
      warm: '#F8F7F5',           // fill_ZT3IAY - теплая поверхность
      cool: 'rgba(238, 238, 238, 0.5)', // fill_GJH6X1 - прохладная поверхность
      muted: 'rgba(238, 238, 238, 0.73)', // fill_FBNN2H - приглушенная поверхность
    },

    // Акцентные цвета
    accent: {
      primary: 'rgba(200, 228, 155, 0.35)', // fill_BOTJTC - основной акцент (зеленоватый)
      secondary: '#FFFBCE',      // fill_HXLA5J - вторичный акцент (желтоватый)
      tertiary: 'rgba(122, 158, 159, 0.41)', // fill_3I4KL0 - третичный акцент (голубоватый)
      success: 'rgba(236, 246, 220, 0.96)', // fill_UISEC2 - успешные действия
      success_alt: '#EDF6DD',    // fill_YP7IAB - альтернативный успех
      warning: 'rgba(253, 224, 131, 0.85)', // fill_895Q2T - предупреждения
      error: 'rgba(116, 116, 116, 0.44)', // fill_EU6BOD - ошибки/недоступные состояния
    },

    // Специальные цвета для telegram
    telegram: {
      blue: '#2AABEE',           // Telegram gradient start
      blue_dark: '#229ED9',      // Telegram gradient end
      accent: '#7A9E9F',         // stroke_UAVW8Z - акцентный цвет telegram элементов
      accent_fill: '#7A9E9F',    // fill_L2JCWU - заливка telegram элементов
    },

    // Границы и разделители
    border: {
      default: 'rgba(0, 0, 0, 0.3)', // stroke_ILBE05 - стандартные границы
      light: 'rgba(0, 0, 0, 0.2)',   // stroke_AW9OHN - светлые границы
      lighter: 'rgba(0, 0, 0, 0.19)', // stroke_R3JXFR - очень светлые границы
    },
  },

  // Типографика
  typography: {
    // Заголовки
    heading: {
      large: {
        fontSize: '20px',        // style_07Y9SH
        fontWeight: '600',
        lineHeight: '1.3em',
        letterSpacing: '0px',
        fontFamily: 'Inter',
      },
      medium: {
        fontSize: '18px',        // style_991H6N, style_ZJQTQD
        fontWeight: '600',
        lineHeight: '1em',
        letterSpacing: '-4%',
        fontFamily: 'Inter',
      },
      small: {
        fontSize: '16px',        // style_8U1PQJ
        fontWeight: '500',
        lineHeight: '1.1em',
        letterSpacing: '-1%',
        fontFamily: 'Inter',
      },
    },

    // Основной текст
    body: {
      large: {
        fontSize: '17px',        // style_MQHC3W
        fontWeight: '400',
        lineHeight: '1.3em',
        letterSpacing: '-3%',
        fontFamily: 'Inter',
      },
      medium: {
        fontSize: '16px',        // style_INPN3S, style_IEY2MM
        fontWeight: '500',
        lineHeight: '1.3em',
        letterSpacing: '0px',
        fontFamily: 'Inter',
      },
      default: {
        fontSize: '15px',        // style_ATDVJF, style_US8IR9, style_YPBUFV
        fontWeight: '400',
        lineHeight: '1.44em',
        letterSpacing: '-6%',
        fontFamily: 'Inter',
      },
      small: {
        fontSize: '14px',        // style_MVJOW8, style_CL3WPL, style_4W79AS
        fontWeight: '400',
        lineHeight: '1.1em',
        letterSpacing: '-2%',
        fontFamily: 'Inter',
      },
      tiny: {
        fontSize: '12px',        // style_A7TYO8
        fontWeight: '500',
        lineHeight: '0.95em',
        letterSpacing: '-1%',
        fontFamily: 'Inter',
      },
    },

    // Специальные стили
    caption: {
      default: {
        fontSize: '16px',        // style_RA7HBM
        fontWeight: '500',
        lineHeight: '1em',
        letterSpacing: '-2%',
        fontFamily: 'Inter',
      },
      bold: {
        fontSize: '15px',        // style_3335PF
        fontWeight: '600',
        lineHeight: '1.3em',
        letterSpacing: '-3%',
        fontFamily: 'Inter',
      },
    },
  },

  // Радиусы скругления
  borderRadius: {
    none: '0px',
    small: '15px',
    medium: '18px',
    default: '20px',
    large: '30px',
    xlarge: '40px',
    full: '50px',            // Для круглых элементов
    button: '20px',          // Стандартный радиус для кнопок
    card: '20px',            // Стандартный радиус для карточек
    overlay: '60px',         // Для overlay элементов
  },

  // Тени
  shadows: {
    // Основные тени
    card: '0px 2px 4px 0px rgba(0, 0, 0, 0.15)',     // effect_QTDMB3
    elevated: '0px 4px 4px 0px rgba(0, 0, 0, 0.12)',  // effect_BTUV9E
    overlay: '0px 4px 4px 0px rgba(0, 0, 0, 0.15)',   // effect_0RYKF6
    button: '0px 0px 4px 0px rgba(0, 0, 0, 0.2)',     // effect_A1LHFP
    image: '0px 4px 4px 0px rgba(0, 0, 0, 0.25)',     // effect_TYDK8J
    bottom: '4px 0px 20px 2px rgba(0, 0, 0, 0.1)',    // effect_MRJ42L

    // Специальные эффекты
    none: 'none',
    backdrop: 'blur(4px)',                             // effect_RJESVP
  },

  // Размеры и отступы
  spacing: {
    // Базовые отступы
    xs: '4px',
    sm: '8px',
    md: '10px',
    lg: '12px',
    xl: '15px',
    '2xl': '16px',
    '3xl': '18px',
    '4xl': '20px',
    '5xl': '25px',
    '6xl': '30px',
    
    // Специфичные отступы из дизайна
    gap: {
      tiny: '4px',           // Для chef recommendations
      small: '6px',          // Для category buttons
      medium: '8px',         // Стандартный gap
      large: '10px',         // Для больших элементов
      xlarge: '12px',        // Для карточек
      '2xlarge': '14px',     // Для info blocks
      '3xlarge': '16px',     // Для schedule blocks
    },
    
    // Внутренние отступы
    padding: {
      button: '10px',        // Стандартный padding для кнопок
      card: '20px',          // Padding для карточек
      section: '15px',       // Padding для секций
      small: '5px',          // Малый padding
    },
  },

  // Размеры элементов
  sizes: {
    // Иконки
    icon: {
      small: '13px',
      medium: '16px',
      large: '20px',
      xlarge: '24px',
      '2xlarge': '28px',
    },

    // Кнопки и интерактивные элементы
    button: {
      height: {
        small: '30px',
        medium: '39px',
        large: '61px',
      },
      width: {
        small: '104px',
        medium: '134px',
        large: '323px',
      },
    },

    // Элементы интерфейса
    dot: {
      small: '8px',          // Для page control dots
      medium: '12px',        // Для status indicators
    },

    // Изображения
    image: {
      small: '26px',         // Для маленьких аватаров
      medium: '46px',        // Для средних аватаров
      large: '100px',        // Для chef recommendations
      hero: '298px',         // Для главных изображений
    },

    // Контейнеры
    container: {
      mobile: '440px',       // Ширина мобильного контейнера
      card: '410px',         // Ширина карточек
    },
  },

  // Состояния элементов
  states: {
    opacity: {
      disabled: '0.3',       // Для неактивных элементов
      muted: '0.35',         // Для приглушенных элементов
      overlay: '0.44',       // Для overlay элементов
    },
  },

  // Анимации и переходы
  transitions: {
    default: '0.2s ease-in-out',
    fast: '0.1s ease-in-out',
    slow: '0.3s ease-in-out',
  },
} as const

// Типы для TypeScript
export type DesignTokens = typeof designTokens
export type ColorTokens = typeof designTokens.colors
export type TypographyTokens = typeof designTokens.typography
export type SpacingTokens = typeof designTokens.spacing
export type SizeTokens = typeof designTokens.sizes

// Экспорт для использования в Tailwind CSS конфигурации
export const tailwindTokens = {
  colors: {
    // Основные цвета
    'bg-primary': designTokens.colors.background.primary,
    'bg-secondary': designTokens.colors.background.secondary,
    'bg-card': designTokens.colors.background.card,
    'bg-overlay': designTokens.colors.background.overlay,
    'bg-disabled': designTokens.colors.background.disabled,
    
    // Текстовые цвета
    'text-primary': designTokens.colors.text.primary,
    'text-secondary': designTokens.colors.text.secondary,
    'text-accent': designTokens.colors.text.accent,
    'text-muted': designTokens.colors.text.muted,
    
    // Поверхности
    'surface-default': designTokens.colors.surface.default,
    'surface-light': designTokens.colors.surface.light,
    'surface-warm': designTokens.colors.surface.warm,
    
    // Акценты
    'accent-primary': designTokens.colors.accent.primary,
    'accent-success': designTokens.colors.accent.success,
    'accent-warning': designTokens.colors.accent.warning,
    
    // Telegram
    'telegram-blue': designTokens.colors.telegram.blue,
    'telegram-accent': designTokens.colors.telegram.accent,
    
    // Границы
    'border-default': designTokens.colors.border.default,
    'border-light': designTokens.colors.border.light,
    'border-lighter': designTokens.colors.border.lighter,
    
    // Белые цвета
    'white-pure': designTokens.colors.white.pure,
    'white-soft': designTokens.colors.white.soft,
  },
  
  borderRadius: designTokens.borderRadius,
  
  boxShadow: designTokens.shadows,
  
  spacing: designTokens.spacing,
  
  fontSize: {
    'heading-lg': designTokens.typography.heading.large.fontSize,
    'heading-md': designTokens.typography.heading.medium.fontSize,
    'heading-sm': designTokens.typography.heading.small.fontSize,
    'body-lg': designTokens.typography.body.large.fontSize,
    'body-md': designTokens.typography.body.medium.fontSize,
    'body-default': designTokens.typography.body.default.fontSize,
    'body-sm': designTokens.typography.body.small.fontSize,
    'body-tiny': designTokens.typography.body.tiny.fontSize,
    'caption-default': designTokens.typography.caption.default.fontSize,
    'caption-bold': designTokens.typography.caption.bold.fontSize,
  },
}

export default designTokens
