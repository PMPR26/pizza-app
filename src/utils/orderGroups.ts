import type { Order } from '@/types'

export interface CheckoutGroup {
  key: string
  orderedAt: string
  lines: Order[]
  grandTotal: number
}

export function groupOrdersByCheckout(orders: Order[]): CheckoutGroup[] {
  const map = new Map<string, Order[]>()
  for (const o of orders) {
    const key = o.checkout_group_id ?? `legacy:${o.id}`
    if (!map.has(key)) map.set(key, [])
    map.get(key)!.push(o)
  }
  const groups: CheckoutGroup[] = []
  for (const [key, lines] of map) {
    lines.sort((a, b) => a.id - b.id)
    const first = lines[0]
    if (!first) continue
    groups.push({
      key,
      orderedAt: first.ordered_at,
      lines,
      grandTotal: lines.reduce((s, l) => s + Number(l.total), 0),
    })
  }
  groups.sort((a, b) => new Date(b.orderedAt).getTime() - new Date(a.orderedAt).getTime())
  return groups
}

export function formatBs(value: number | string): string {
  const n = typeof value === 'string' ? parseFloat(value) : value
  return Number.isNaN(n) ? '0.00 Bs' : `${n.toFixed(2)} Bs`
}
