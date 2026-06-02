from pathlib import Path

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
    draw.polygon(
        [(x2, y2), (x2 - direction * 16, y2 - 8), (x2 - direction * 16, y2 + 8)],
        fill=color,
    )


def self_arrow(draw, x, y, color=(40, 55, 70), width=4):
    draw.line((x, y, x + 58, y, x + 58, y + 28, x, y + 28), fill=color, width=width)
    draw.polygon([(x, y + 28), (x + 14, y + 20), (x + 14, y + 36)], fill=color)


def render(title, participants, messages, regions, filename, region_color):
    width = 2500
    left = 120
    top = 150
    box_w = 225
    box_h = 78
    row_h = 60
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

    for label, start, end in regions:
        y1 = y0 + (start - 1) * row_h - 24
        y2 = y0 + end * row_h + 18
        fill, outline = region_color
        draw.rounded_rectangle((55, y1, width - 55, y2), radius=18, fill=fill, outline=outline, width=3)
        draw.text((78, y1 + 12), label, font=small_font, fill=outline)

    for x in xs.values():
        draw.line((x, top + box_h, x, bottom), fill=(145, 154, 164), width=3)

    for idx, (src, dst, label) in enumerate(messages, 1):
        y = y0 + (idx - 1) * row_h
        if src == dst:
            self_arrow(draw, xs[src], y)
            draw_wrapped(draw, (xs[src] + 95, y - 7), label, msg_font, (20, 32, 44), 300, anchor="left")
        else:
            x1, x2 = xs[src], xs[dst]
            arrow(draw, (x1, y), (x2, y))
            draw_wrapped(draw, ((x1 + x2) / 2, y - 18), label, msg_font, (20, 32, 44), max(230, abs(x2 - x1) - 40))

    img.save(OUT / filename)


red = ((255, 226, 226), (188, 45, 45))
green = ((226, 255, 232), (34, 139, 74))


password_reset_participants = [
    "Attacker / User",
    "login.php Reset Form",
    "assets/js/login.js",
    "resetPassword.php",
    "MySQL user table",
    "Browser",
]

password_reset_before = [
    ("Attacker / User", "login.php Reset Form", "Open public reset page without login"),
    ("Attacker / User", "login.php Reset Form", "Enter username + new password"),
    ("login.php Reset Form", "assets/js/login.js", "Click Reset Password"),
    ("assets/js/login.js", "resetPassword.php", "POST username, password1, password2"),
    ("resetPassword.php", "resetPassword.php", "Validate fields and matching new passwords"),
    ("resetPassword.php", "MySQL user table", "SELECT user WHERE username = submitted username"),
    ("MySQL user table", "resetPassword.php", "Return matching account"),
    ("resetPassword.php", "resetPassword.php", "No old password, token, OTP, or session check"),
    ("resetPassword.php", "MySQL user table", "UPDATE user SET password = new hash"),
    ("MySQL user table", "resetPassword.php", "Password changed"),
    ("resetPassword.php", "Browser", "Show reset success message"),
    ("Attacker / User", "Browser", "Login using new password"),
]

password_reset_after = [
    ("Attacker / User", "login.php Reset Form", "Open reset page"),
    ("Attacker / User", "login.php Reset Form", "Enter username, current password, new password"),
    ("login.php Reset Form", "assets/js/login.js", "Click Reset Password"),
    ("assets/js/login.js", "resetPassword.php", "POST username, current password, password1, password2"),
    ("resetPassword.php", "resetPassword.php", "Require current password field"),
    ("resetPassword.php", "MySQL user table", "SELECT user WHERE username = submitted username"),
    ("MySQL user table", "resetPassword.php", "Return stored password hash"),
    ("resetPassword.php", "resetPassword.php", "Verify current password with password_verify()"),
    ("resetPassword.php", "Browser", "Reject request if current password is missing or wrong"),
    ("resetPassword.php", "resetPassword.php", "Hash new password with password_hash()"),
    ("resetPassword.php", "MySQL user table", "UPDATE user SET password = secure new hash"),
    ("MySQL user table", "resetPassword.php", "Password changed only after verification"),
    ("resetPassword.php", "Browser", "Show reset success message"),
]

render(
    "Before Mitigation: Insecure Password Reset PHP Flow",
    password_reset_participants,
    password_reset_before,
    [
        ("VULNERABLE: password reset page is publicly accessible", 1, 4),
        ("VULNERABLE: backend updates password without identity verification", 5, 10),
    ],
    "password_reset_before_sequence.png",
    red,
)

render(
    "After Mitigation: Secured Password Reset PHP Flow",
    password_reset_participants,
    password_reset_after,
    [
        ("MITIGATION: current password is required and sent to backend", 2, 5),
        ("MITIGATION: password_verify() confirms account ownership before update", 6, 13),
    ],
    "password_reset_after_sequence.png",
    green,
)


hashing_participants = [
    "User",
    "Register / Reset Form",
    "register.php / resetPassword.php",
    "MySQL user table",
    "Login Form",
    "checkLogin.php",
    "Browser",
]

hashing_before = [
    ("User", "Register / Reset Form", "Submit plaintext password"),
    ("Register / Reset Form", "register.php / resetPassword.php", "POST password fields"),
    ("register.php / resetPassword.php", "register.php / resetPassword.php", "md5(password) creates fast unsalted 32-character hash"),
    ("register.php / resetPassword.php", "MySQL user table", "Store MD5 hash in user.password"),
    ("MySQL user table", "register.php / resetPassword.php", "MD5 hash stored"),
    ("User", "Login Form", "Submit username + password"),
    ("Login Form", "checkLogin.php", "POST loginUsername + loginPassword"),
    ("checkLogin.php", "checkLogin.php", "md5(loginPassword)"),
    ("checkLogin.php", "MySQL user table", "SELECT user WHERE username AND password = MD5 hash"),
    ("MySQL user table", "checkLogin.php", "Return account if hash matches"),
    ("checkLogin.php", "Browser", "Create session and show login success"),
]

hashing_after = [
    ("User", "Register / Reset Form", "Submit plaintext password"),
    ("Register / Reset Form", "register.php / resetPassword.php", "POST password fields"),
    ("register.php / resetPassword.php", "register.php / resetPassword.php", "password_hash(password, PASSWORD_DEFAULT)"),
    ("register.php / resetPassword.php", "MySQL user table", "Store salted adaptive hash in user.password"),
    ("MySQL user table", "register.php / resetPassword.php", "bcrypt-style hash stored"),
    ("User", "Login Form", "Submit username + password"),
    ("Login Form", "checkLogin.php", "POST loginUsername + loginPassword"),
    ("checkLogin.php", "MySQL user table", "SELECT user WHERE username only"),
    ("MySQL user table", "checkLogin.php", "Return stored password_hash value"),
    ("checkLogin.php", "checkLogin.php", "password_verify(loginPassword, stored hash)"),
    ("checkLogin.php", "Browser", "Create session only if password_verify() succeeds"),
]

render(
    "Before Mitigation: Weak MD5 Password Hashing PHP Flow",
    hashing_participants,
    hashing_before,
    [
        ("VULNERABLE: passwords are converted with md5()", 3, 5),
        ("VULNERABLE: login compares fast unsalted MD5 hashes", 8, 10),
    ],
    "md5_hashing_before_sequence.png",
    red,
)

render(
    "After Mitigation: Secure Password Hashing PHP Flow",
    hashing_participants,
    hashing_after,
    [
        ("MITIGATION: passwords are stored using password_hash()", 3, 5),
        ("MITIGATION: login uses username lookup + password_verify()", 8, 11),
    ],
    "md5_hashing_after_sequence.png",
    green,
)

print(OUT / "password_reset_before_sequence.png")
print(OUT / "password_reset_after_sequence.png")
print(OUT / "md5_hashing_before_sequence.png")
print(OUT / "md5_hashing_after_sequence.png")
