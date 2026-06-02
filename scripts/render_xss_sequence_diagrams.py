from pathlib import Path
from textwrap import wrap

from PIL import Image, ImageDraw, ImageFont


ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / "diagrams"
OUT.mkdir(exist_ok=True)

FONT = "/System/Library/Fonts/Supplemental/Arial.ttf"
BOLD = "/System/Library/Fonts/Supplemental/Arial Bold.ttf"


def font(size, bold=False):
    return ImageFont.truetype(BOLD if bold else FONT, size)


def text_size(draw, text, fnt):
    box = draw.textbbox((0, 0), text, font=fnt)
    return box[2] - box[0], box[3] - box[1]


def draw_wrapped(draw, xy, text, fnt, fill, max_width, line_gap=4, anchor="center"):
    words = text.split()
    lines = []
    current = ""
    for word in words:
        test = word if not current else f"{current} {word}"
        if text_size(draw, test, fnt)[0] <= max_width:
            current = test
        else:
            if current:
                lines.append(current)
            current = word
    if current:
        lines.append(current)

    x, y = xy
    line_h = text_size(draw, "Ag", fnt)[1] + line_gap
    total_h = line_h * len(lines) - line_gap
    start_y = y - total_h / 2 if anchor == "center" else y
    for i, line in enumerate(lines):
        w, _ = text_size(draw, line, fnt)
        line_x = x - w / 2 if anchor == "center" else x
        draw.text((line_x, start_y + i * line_h), line, font=fnt, fill=fill)


def arrow(draw, start, end, color=(40, 55, 70), width=4):
    x1, y1 = start
    x2, y2 = end
    draw.line((x1, y1, x2, y2), fill=color, width=width)
    direction = 1 if x2 >= x1 else -1
    pts = [
        (x2, y2),
        (x2 - direction * 16, y2 - 8),
        (x2 - direction * 16, y2 + 8),
    ]
    draw.polygon(pts, fill=color)


def self_arrow(draw, x, y, color=(40, 55, 70), width=4):
    draw.line((x, y, x + 58, y, x + 58, y + 28, x, y + 28), fill=color, width=width)
    draw.polygon([(x, y + 28), (x + 14, y + 20), (x + 14, y + 36)], fill=color)


def render(title, participants, messages, regions, filename, region_color):
    width = 2600
    left = 110
    top = 150
    box_w = 210
    box_h = 78
    row_h = 58
    y0 = top + 125
    bottom = y0 + row_h * (len(messages) + 2)
    height = bottom + 90
    col_gap = (width - 2 * left) / (len(participants) - 1)
    xs = {name: left + i * col_gap for i, name in enumerate(participants)}

    img = Image.new("RGB", (width, height), "white")
    draw = ImageDraw.Draw(img)

    title_font = font(36, bold=True)
    header_font = font(20, bold=True)
    msg_font = font(20)
    small_font = font(18, bold=True)

    draw.text((left, 45), title, font=title_font, fill=(25, 35, 45))

    for name, x in xs.items():
        draw.rounded_rectangle(
            (x - box_w / 2, top, x + box_w / 2, top + box_h),
            radius=14,
            fill=(242, 246, 250),
            outline=(86, 107, 128),
            width=3,
        )
        draw_wrapped(draw, (x, top + box_h / 2), name, header_font, (25, 35, 45), box_w - 20)
        draw.line((x, top + box_h, x, bottom), fill=(185, 193, 202), width=3)

    # Region backgrounds.
    for label, start, end in regions:
        y1 = y0 + (start - 1) * row_h - 22
        y2 = y0 + end * row_h + 18
        fill, outline = region_color
        draw.rounded_rectangle((55, y1, width - 55, y2), radius=18, fill=fill, outline=outline, width=3)
        draw.text((78, y1 + 12), label, font=small_font, fill=outline)

    # Redraw lifelines over region backgrounds.
    for x in xs.values():
        draw.line((x, top + box_h, x, bottom), fill=(145, 154, 164), width=3)

    for idx, msg in enumerate(messages, 1):
        src, dst, label = msg
        y = y0 + (idx - 1) * row_h
        if src == dst:
            self_arrow(draw, xs[src], y)
            label_x = xs[src] + 95
            draw_wrapped(draw, (label_x, y - 7), label, msg_font, (20, 32, 44), 280, anchor="left")
        else:
            x1, x2 = xs[src], xs[dst]
            arrow(draw, (x1, y), (x2, y))
            mid = (x1 + x2) / 2
            max_width = max(220, abs(x2 - x1) - 40)
            draw_wrapped(draw, (mid, y - 18), label, msg_font, (20, 32, 44), max_width)

    img.save(OUT / filename)


participants = [
    "Attacker",
    "Upload / Input",
    "updateImage.php",
    "MySQL item table",
    "Victim / User",
    "Search Page",
    "itemDetailsSearchTableCreator.php",
    "getItemDetailsForPopover.php",
    "Browser",
]

before_messages = [
    ("Attacker", "Upload / Input", "Submit malicious filename or stored payload"),
    ("Upload / Input", "updateImage.php", "POST itemImageItemNumber + itemImageFile"),
    ("updateImage.php", "updateImage.php", "basename(original uploaded filename)"),
    ("updateImage.php", "updateImage.php", "$fileName = time() + '_' + original filename"),
    ("updateImage.php", "MySQL item table", "UPDATE item SET imageURL = $fileName"),
    ("MySQL item table", "updateImage.php", "Malicious value stored"),
    ("Victim / User", "Search Page", "Open Search / Item Details table"),
    ("Search Page", "itemDetailsSearchTableCreator.php", "Load itemDetailsSearchTableCreator.php"),
    ("itemDetailsSearchTableCreator.php", "MySQL item table", "SELECT * FROM item"),
    ("MySQL item table", "itemDetailsSearchTableCreator.php", "Return itemName, imageURL, description"),
    ("itemDetailsSearchTableCreator.php", "itemDetailsSearchTableCreator.php", "Concatenate $row['itemName'] directly into HTML"),
    ("itemDetailsSearchTableCreator.php", "Browser", "<td><a>$row['itemName']</a></td>"),
    ("Victim / User", "Search Page", "Hover over item name"),
    ("Search Page", "getItemDetailsForPopover.php", "AJAX POST productID"),
    ("getItemDetailsForPopover.php", "MySQL item table", "SELECT * FROM item WHERE productID = :productID"),
    ("MySQL item table", "getItemDetailsForPopover.php", "Return imageURL and item details"),
    ("getItemDetailsForPopover.php", "getItemDetailsForPopover.php", "Concatenate $row['imageURL'] directly into <img src>"),
    ("getItemDetailsForPopover.php", "Browser", "<img src='.../$row[imageURL]'>"),
    ("Browser", "Browser", "Payload breaks out of attribute and onerror executes"),
    ("Browser", "Victim / User", "Stored XSS alert appears"),
]

after_messages = [
    ("Attacker", "Upload / Input", "Submit malicious filename or stored payload"),
    ("Upload / Input", "updateImage.php", "POST itemImageItemNumber + itemImageFile"),
    ("updateImage.php", "updateImage.php", "Extract extension only"),
    ("updateImage.php", "updateImage.php", "Validate extension against jpg/jpeg/png/gif"),
    ("updateImage.php", "updateImage.php", "$fileName = time() + '_' + bin2hex(random_bytes(8)) + extension"),
    ("updateImage.php", "MySQL item table", "UPDATE item SET imageURL = safe generated filename"),
    ("MySQL item table", "updateImage.php", "Safe value stored"),
    ("Victim / User", "Search Page", "Open Search / Item Details table"),
    ("Search Page", "itemDetailsSearchTableCreator.php", "Load itemDetailsSearchTableCreator.php"),
    ("itemDetailsSearchTableCreator.php", "MySQL item table", "SELECT * FROM item"),
    ("MySQL item table", "itemDetailsSearchTableCreator.php", "Return itemName, imageURL, description"),
    ("itemDetailsSearchTableCreator.php", "itemDetailsSearchTableCreator.php", "htmlspecialchars($row['itemName'], ENT_QUOTES, 'UTF-8')"),
    ("itemDetailsSearchTableCreator.php", "Browser", "Render encoded table values"),
    ("Victim / User", "Search Page", "Hover over item name"),
    ("Search Page", "getItemDetailsForPopover.php", "AJAX POST productID"),
    ("getItemDetailsForPopover.php", "MySQL item table", "SELECT * FROM item WHERE productID = :productID"),
    ("MySQL item table", "getItemDetailsForPopover.php", "Return imageURL and item details"),
    ("getItemDetailsForPopover.php", "getItemDetailsForPopover.php", "htmlspecialchars($row['imageURL'], ENT_QUOTES, 'UTF-8')"),
    ("getItemDetailsForPopover.php", "Browser", "<img src='.../encoded-imageURL'>"),
    ("Browser", "Browser", "Payload treated as text, no JavaScript execution"),
    ("Browser", "Victim / User", "Page loads safely"),
]

render(
    "Before Mitigation: Stored XSS Vulnerable PHP Flow",
    participants,
    before_messages,
    [
        ("VULNERABLE: attacker-controlled filename is reused", 3, 6),
        ("VULNERABLE: stored item values rendered without output encoding", 11, 12),
        ("VULNERABLE: imageURL rendered directly into HTML attribute", 17, 20),
    ],
    "stored_xss_before_sequence.png",
    ((255, 226, 226), (188, 45, 45)),
)

render(
    "After Mitigation: Stored XSS Secured PHP Flow",
    participants,
    after_messages,
    [
        ("MITIGATION: safe server-generated filename", 3, 7),
        ("MITIGATION: encode table output with htmlspecialchars()", 12, 13),
        ("MITIGATION: encode popover output with htmlspecialchars()", 18, 21),
    ],
    "stored_xss_after_sequence.png",
    ((226, 255, 232), (34, 139, 74)),
)

print(OUT / "stored_xss_before_sequence.png")
print(OUT / "stored_xss_after_sequence.png")
