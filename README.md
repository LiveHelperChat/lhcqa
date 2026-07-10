# LHC QA — Quality Assessment Extension

This extension adds Quality Assessment (QA) scoring for chat conversations in Live Helper Chat. Operators can fill QA evaluation forms for closed chats, and QA scores are displayed in the chat list and agent statistics.

---

## Features

- **QA column in the chat list** — shows QA scores and allows filling/viewing QA evaluations directly from the chat list.
- **Agent statistics integration** — displays per-agent "QA Score" (average %) and "QA Chats" (count of evaluated chats) in the agent statistic table and department performance widget.
- **Settings page** — configure which internal form to use for QA evaluations.
- **Export support** — QA columns are included in XLS exports of agent statistics.

---

## Setup

1. Copy the `lhcqa` folder into `extension/lhcqa/`.
2. In `lhc_web/settings/settings.ini.default.php`, add `lhcqa` to the extensions list:
   ```ini
   'extensions' => ['lhcqa']
   ```
3. Clear the cache from the Live Helper Chat admin panel.

4. Create an **internal form** (Admin → Forms) that operators will use during QA evaluation. Add rating fields (e.g., stars, dropdowns, text areas) as needed. The form's `attr_int_1` field will be used as the QA score.

5. Go to **System → QA Forms** (`/site_admin/qaform/settings`) and select the form you created.

---

## Permissions

The extension defines the following permission functions under the `lhqaform` module:

| Permission | Module / Function | Description |
|---|---|---|
| **Configure QA** | `lhqaform` → `configure` | Access the QA Forms settings page to choose which form to use. |
| **See QA details** | `lhqaform` → `see_detailed` | View detailed QA evaluation results for a chat. |
| **Fill QA form** | `lhqaform` → `fill` | Permission to fill the QA evaluation form. |
| **Fill private forms** | `lhform` → `fill_private` | Required alongside `lhqaform,fill` to submit QA evaluations via the chat list modal. |

---

## Usage Scenarios

### 1. View QA score and details only (read-only)

Give the operator these permissions:

- **`lhqaform` → `see_detailed`**

With this permission, the operator will:

- See a **"QA" column** in the chat list.
- For chats that already have a QA evaluation, the QA score (e.g., `85%`) is shown as a **clickable button**.
- Clicking the score opens a modal displaying the **detailed QA evaluation** (filled form fields).

> The operator **cannot** fill or submit new QA evaluations.

---

### 2. View QA score and fill QA evaluations

Give the operator these permissions:

- **`lhqaform` → `fill`**
- **`lhform` → `fill_private`**

Optionally also grant:

- **`lhqaform` → `see_detailed`** — to view detailed results of previously filled QA forms.

With these permissions, the operator will:

- See a **"QA" column** in the chat list.
- See a **"QA" button** next to each chat in the list.
- Clicking the button opens a **modal** with the chat conversation on the left and the QA evaluation form on the right.
- After submission, the QA score is stored in the chat's `chat_variables` and displayed in the column.

---

### 3. Configure QA settings (admin)

Give the operator:

- **`lhqaform` → `configure`**

This grants access to **System → QA Forms** (`/site_admin/qaform/settings`) where the QA evaluation form can be selected.

---

## Permission Assignment

1. Go to **System → Roles** in the admin panel.
2. Create or edit a role (e.g., "QA Reviewer").
3. Under **Module:** select `lhqaform` and check the desired functions.
4. Also check `lhform` → `fill_private` if the role should be able to fill QA evaluations.
5. Assign the role to users or groups under **System → Groups**.

---

## How It Works

1. An admin selects an internal form via **QA Forms Settings**.
2. When an operator fills the QA form for a chat:
   - The form submission is stored in `lh_abstract_form_collected`.
   - The QA score (`attr_int_1` of the collected form) is saved to the chat's `chat_variables` as `qa_chat_score`.
3. The chat list "QA" column reads `qa_chat_score` from chat variables.
4. Agent statistics aggregate QA scores and chat counts per operator from `lh_abstract_form_collected` joined with `lh_chat`.

---

## Templates Overridden

| Path | Purpose |
|---|---|
| `lhchat/lists_chats_parts/column_end_multiinclude.tpl.php` | Adds "QA" column header in chat list |
| `lhchat/lists_chats_parts/column_end_row_multiinclude.tpl.php` | Adds QA score/button in each chat row |
| `lhstatistic/tabs/part/agentstatistic/table_header_after_offline_multiinclude.tpl.php` | Adds QA columns to agent statistic table |
| `lhstatistic/tabs/part/agentstatistic/table_content_after_offline_multiinclude.tpl.php` | Populates QA data in agent statistic rows |
| `lhstatistic/tabs/part/agentstatistic/table_average_after_offline_multiinclude.tpl.php` | Adds QA averages row |
| `lhstatistic/tabs/part/agentstatistic/table_header_collspans_append_multiinclude.tpl.php` | Adjusts colspan for QA columns |
| `pagelayouts/parts/modules_menu/extension_module_multiinclude.tpl.php` | Adds "QA Forms" menu link |
