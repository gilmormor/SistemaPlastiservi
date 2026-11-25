# Reasignar una producción a otro cliente u otra nota de venta

**Documento para revisar con Patricio Olave**
Plastiservi — 24/11/2026

---

## El problema

Un cliente hace un pedido. Su nota de venta entra a producción, se fabrica el
producto, y el lote queda en bodega **a nombre de esa nota de venta**.

Después pasa algo: otro cliente lo necesita con urgencia, el pedido original se
atrasa, se decide entregarlo a otro. **Ese material se le da a alguien más.**

Hoy el sistema no permite hacerlo. El lote queda atado a la nota de venta que lo
originó y no hay forma de cambiarlo. En la práctica se resuelve por fuera, y el
sistema queda diciendo algo que no es verdad.

### Ejemplo real, de la base de prueba

El producto 3626 tiene **140 kg comprometidos**, repartidos en tres lotes:

| Lote | Cantidad | Fabricado para |
|------|---------:|----------------|
| 40   | 10 (ya salió) | NV 35503 — SERVICIOS Y EMPAQUE LTDA |
| 48   | 90       | NV 35503 — SERVICIOS Y EMPAQUE LTDA |
| 69   | 50       | NV 35503 — SERVICIOS Y EMPAQUE LTDA |

Si quisiéramos darle los 50 kg del lote 69 a otro cliente, hoy no hay cómo.

---

## La idea

**Nada se borra. Se suma un hecho nuevo.**

El lote 69 se fabricó para la NV 35503. Eso es cierto y va a seguir siéndolo para
siempre: la orden de trabajo, la orden de producción y el registro de producción
nacieron de ese pedido. No se toca.

Lo que se agrega es un segundo hecho: **el 24/11/2026, Gilmer Moreno asignó ese
lote a INDUSTRIAS X (NV 35510), porque el cliente lo necesitaba con urgencia.**

Las dos cosas conviven, y cualquiera puede ver las dos:

```
Lote 69  ·  50 kg  ·  producto 3626

    Fabricado para   →  NV 35503  SERVICIOS Y EMPAQUE LTDA
    Entregado a      →  NV 35510  INDUSTRIAS X
                        24/11/2026 · Gilmer Moreno
                        "Cliente lo necesitaba con urgencia"
```

---

## Qué pasa con la nota de venta que pierde su producción

Esta es la parte importante, y la que necesita su decisión.

### Queda descubierta, y tiene que notarse

La NV 35503 sigue marcada como **"requiere fabricación"**, porque efectivamente
lo requiere: su producto ya no está. Vuelve a estar pendiente.

### La ejecutiva tiene que enterarse al hacer la solicitud de despacho

Hoy, una nota de venta que requiere fabricación y no tiene stock propio muestra
en la pantalla de solicitud de despacho el mensaje **"En proceso de fabricación"**.

Si no hacemos nada, la NV 35503 mostraría eso mismo — **y sería falso**. Su
producto ya se fabricó; se lo dieron a otro. La ejecutiva se quedaría esperando
un material que no va a llegar, sin entender por qué.

Así que en esa pantalla debe aparecer, en lugar del mensaje genérico:

> ⚠ **El lote #69 (50 kg), fabricado para esta nota de venta, fue asignado el
> 24/11/2026 a INDUSTRIAS X — NV 35510, por Gilmer Moreno.**
> Motivo: cliente lo necesitaba con urgencia.
> Esta nota de venta requiere una nueva fabricación o que se le asigne stock.

Sin ese aviso, el problema se descubre tarde y con el cliente reclamando.

### ¿Y cómo se le consigue el producto de nuevo?

Dos caminos posibles. **Esto es lo que hay que decidir**, porque cambian el
trabajo de planta:

**Opción A — Reabrir el ítem de la nota de venta.**
El ítem vuelve a la cola de programación como si nunca se hubiera enviado. La OT
original queda cerrada, con su producción, que se entregó a otro.
*A favor:* es el camino natural, el ítem sigue su flujo normal.
*En contra:* hay que permitir reabrir algo que el sistema da por terminado, y eso
toca el control de qué está pendiente de fabricar.

**Opción B — Generar una orden de trabajo nueva desde esa nota de venta.**
La OT original no se toca. Se crea otra para volver a fabricar lo mismo.
*A favor:* no se reabre nada, cada OT cuenta su propia historia.
*En contra:* la nota de venta queda con dos OT y hay que saber leer que la
primera se entregó a otro cliente.

**Opción C — Asignarle stock existente**, si lo hay en bodega. Esto ya quedaría
resuelto con el módulo de Reserva de Stock, sin fabricar nada.

---

## Reglas que el sistema haría cumplir

1. **Mismo producto.** No se puede reasignar un lote a una nota de venta de otro
   producto.

2. **No se reasigna lo ya despachado.** Si el material salió de bodega, se acabó:
   está en el camión o en el cliente. (El lote 40 del ejemplo es justo ese caso.)

3. **Motivo obligatorio.** Alguien va a preguntar por qué su pedido se atrasó, y
   tiene que haber respuesta.

4. **Decidir en el momento.** La pantalla obliga a elegir qué pasa con la nota de
   venta que queda descubierta: se le asigna otro stock ahí mismo, o se acepta
   explícitamente que queda esperando.

5. **Queda registrado quién y cuándo.** No se puede deshacer en silencio.

---

## Qué se ve y dónde

| Pantalla | Qué muestra |
|---|---|
| Reasignación (nueva) | Buscar el lote, ver para quién se fabricó, elegir el nuevo destino, motivo |
| Solicitud de despacho | El aviso a la ejecutiva de la NV que perdió su producción |
| Trazabilidad por documento | Las dos cosas: de qué NV nació y a cuál se entregó |
| Reporte de stock | El comprometido pasa a contar para la nota de venta nueva |
| Etiqueta del lote | Pendiente de decidir: ¿debe decir a quién se entregó? |

---

## Lo que NO cambia

- **La orden de trabajo y la orden de producción no se tocan.** Se fabricó lo que
  se fabricó, para quien lo pidió.
- **El inventario no se mueve.** El material está físicamente donde está; lo que
  cambia es a quién se le entrega.
- **El picking sigue igual.** Cuando el material se aparte físicamente, será para
  el cliente nuevo.

---

## Preguntas para la reunión

1. ¿Opción A (reabrir el ítem) u opción B (nueva orden de trabajo) para la nota
   de venta que queda descubierta?
2. ¿Quién puede reasignar? ¿Producción, comercial, los dos? ¿Hace falta que lo
   autorice alguien?
3. ¿Se puede reasignar un lote **parcialmente** (30 de los 50 kg a otro cliente)
   o siempre completo?
4. ¿Se avisa al vendedor de la nota de venta afectada, o basta con que lo vea la
   ejecutiva al despachar?
5. ¿La etiqueta del lote debe reimprimirse con el cliente nuevo?
