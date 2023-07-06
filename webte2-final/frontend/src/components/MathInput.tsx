import { useEffect, useRef } from 'react'
import { MathfieldElement } from 'mathlive'

import 'mathlive/fonts.css'
import { useEffectOnce } from '@/hooks/useEffectOnce'

export type MathfieldProps = {
  value?: string
  onChange?: (latex: string) => void
  className?: string
}

// TODO: fix sound file errors
export default function MathInput({ value, onChange, className }: MathfieldProps) {
  const mathfieldRef = useRef<MathfieldElement>(null)

  useEffect(() => {
    if (!mathfieldRef.current) return

    const mathfield = mathfieldRef.current

    if (value) {
      mathfield.value = value
    }
  }, [value])

  useEffectOnce(() => {
    if (!mathfieldRef.current) return

    const mathfield = mathfieldRef.current
    const handler = () => {
      onChange?.(mathfield.value)
    }

    mathfield.addEventListener('input', handler)

    return () => {
      mathfield.removeEventListener('input', handler)
    }
  })

  return (
    <math-field
      class={'math-field ' + (className ?? '')}
      default-mode='math'
      ref={mathfieldRef} />
  )
}
