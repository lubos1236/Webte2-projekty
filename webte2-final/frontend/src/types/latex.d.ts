declare module 'latex.js' {
  export function parse(latex: string, options?: ParseOptions): any

  interface ParseOptions {
    generator?: Generator
  }

  export interface Generator {
    create(document: any): any
  }

  export class HtmlGenerator implements Generator {
    constructor(options?: HtmlGeneratorOptions)

    create(element: string): HTMLElement
  }

  interface HtmlGeneratorOptions {
    hyphenate?: boolean
    CustomMacros?: any
  }
}
