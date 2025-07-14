import { Plugin } from 'vite'
import fg from 'fast-glob'
import fse from 'fs-extra'
import path from 'path'
import { createHash } from 'crypto'

interface GenerateIconMapOptions {
  svgDir?: string
  outputPath?: string
  prefix?: string
  debounceMs?: number
}

export default function generateIconMapPlugin(options: GenerateIconMapOptions = {}): Plugin {
  const {
    svgDir = 'resources/shared/svg',
    outputPath = 'resources/shared/ui/customIcons.ts',
    prefix = '',
    debounceMs = 100
  } = options

  let lastContentHash = ''
  let debounceTimer: NodeJS.Timeout | null = null
  let isGenerating = false

  const generateIconMap = async () => {
    if (isGenerating) return
    isGenerating = true

    try {
      // Получаем кастомные SVG иконки
      const customIcons = await fg(`${svgDir}/**/*.svg`)

      if (customIcons.length === 0) {
        console.warn(`[generate-icon-map] No SVG files found in ${svgDir}`)
        // Генерируем пустой файл если нет иконок
        const emptyContent = `// AUTO-GENERATED FILE. DO NOT EDIT.
// Generated from: ${svgDir}

export const customIconMap = {} as const

export type CustomIconName = keyof typeof customIconMap
`
        const resolvedOutputPath = path.resolve(process.cwd(), outputPath)
        await fse.ensureDir(path.dirname(resolvedOutputPath))
        await fse.writeFile(resolvedOutputPath, emptyContent, 'utf-8')
        
        lastContentHash = createHash('md5').update(emptyContent).digest('hex')
        console.log(`[generate-icon-map] Generated empty icon map`)
        isGenerating = false
        return
      }

      // Фильтруем только существующие файлы
      const existingIcons = []
      for (const file of customIcons) {
        try {
          await fse.access(file)
          existingIcons.push(file)
        } catch {
          // Файл не существует, пропускаем
          console.warn(`[generate-icon-map] Skipping non-existent file: ${file}`)
        }
      }

      const customMap = existingIcons.map(file => {
        const name = path.basename(file, '.svg')
        const iconName = prefix ? `${prefix}${name}` : name
        return `  "${iconName}": () => import('~icons/custom/${name}')`
      })

      const content = `// AUTO-GENERATED FILE. DO NOT EDIT.
// Generated from: ${svgDir}

export const customIconMap = {
${customMap.join(',\n')}
} as const

export type CustomIconName = keyof typeof customIconMap
`

      // Проверяем, изменился ли контент (кеширование)
      const contentHash = createHash('md5').update(content).digest('hex')
      if (contentHash === lastContentHash) {
        isGenerating = false
        return
      }

      const resolvedOutputPath = path.resolve(process.cwd(), outputPath)
      await fse.ensureDir(path.dirname(resolvedOutputPath))
      await fse.writeFile(resolvedOutputPath, content, 'utf-8')

      lastContentHash = contentHash
      console.log(`[generate-icon-map] Generated ${customMap.length} custom icons`)
    } catch (error) {
      console.error('[generate-icon-map] Error generating custom icon map:', error)
    } finally {
      isGenerating = false
    }
  }

  const debouncedGenerate = () => {
    if (debounceTimer) {
      clearTimeout(debounceTimer)
    }
    debounceTimer = setTimeout(generateIconMap, debounceMs)
  }

  const handleFileChange = (file: string) => {
    if (file.includes(svgDir) && file.endsWith('.svg')) {
      debouncedGenerate()
    }
  }

  return {
    name: 'generate-icon-map',
    
    async config() {
      await generateIconMap()
    },
    
    async configureServer(server) {
      // Отслеживаем изменения в папке с кастомными иконками
      server.watcher.add(svgDir)
      
      server.watcher.on('add', handleFileChange)
      server.watcher.on('change', handleFileChange)
      server.watcher.on('unlink', handleFileChange)
    },

    buildEnd() {
      // Очищаем таймер при завершении сборки
      if (debounceTimer) {
        clearTimeout(debounceTimer)
      }
    }
  }
} 