# AI Strategy — Cloud ERP Only / Advisory

## Placement
AI لا يثبت على Edge. يعمل في Main ERP/Cloud.

## Inputs
- synchronized sales.
- inventory history.
- purchase history.
- food/stock cost.
- customer trends.
- branch performance.

## Outputs
Recommendation records مثل:
- "المخزون المتوقع للصنف X سينخفض تحت الحد خلال 3 أيام."
- "متوسط استهلاك Y ارتفع 18%."
- "يفضل مراجعة شراء Z قبل نهاية الأسبوع."
- "الصنف A عالي الشعبية منخفض الهامش."

## Recommendation Workflow
`GENERATED → REVIEWED → ACCEPTED|DISMISSED`

قبول Recommendation لا ينفذ العملية تلقائيًا؛ يفتح للمستخدم الشاشة المناسبة أو Draft فقط إذا تم اعتماد ذلك مستقبلًا.

## Hard Rule V1
AI service لا يملك صلاحية مباشرة لكتابة:
- purchase orders
- prices
- stock adjustments
- payments
- refunds
- branch configurations
