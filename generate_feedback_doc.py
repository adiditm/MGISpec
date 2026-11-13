import os
import subprocess
import docx
from docx.shared import Pt, Inches, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=70, bottom=70, left=100, right=100):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('top', top), ('bottom', bottom), ('left', left), ('right', right)]:
        node = OxmlElement(f'w:{m}')
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def prevent_row_split(row):
    trPr = row._tr.get_or_add_trPr()
    trPr.append(parse_xml(f'<w:cantSplit {nsdecls("w")}/>'))

def create_feedback_doc():
    doc = docx.Document()

    # Page Margins: 0.65 in top/bottom, 0.8 in left/right
    for section in doc.sections:
        section.top_margin = Inches(0.65)
        section.bottom_margin = Inches(0.65)
        section.left_margin = Inches(0.8)
        section.right_margin = Inches(0.8)

    # Document Header / Title
    title = doc.add_paragraph()
    title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    title.paragraph_format.space_before = Pt(0)
    title.paragraph_format.space_after = Pt(2)
    
    r1 = title.add_run('DOKUMEN RANGKUMAN FEEDBACK & KESEPAKATAN SISTEM\n')
    r1.bold = True
    r1.font.size = Pt(12.5)
    r1.font.color.rgb = RGBColor(30, 41, 59)

    r2 = title.add_run('WEBSITE KATALOG PRODUK & PANEL CMS MGISPEC (27 OKTOBER 2026)')
    r2.bold = True
    r2.font.size = Pt(13)
    r2.font.color.rgb = RGBColor(15, 23, 42)

    # Metadata
    p_meta = doc.add_paragraph()
    p_meta.paragraph_format.space_before = Pt(6)
    p_meta.paragraph_format.space_after = Pt(4)
    p_meta.paragraph_format.line_spacing = 1.12
    
    p_meta.add_run('Sumber Masukan: ').bold = True
    p_meta.add_run('Bpk Bram Setiawan Liem (MGISpec / MGIClub Global)\n')
    p_meta.add_run('Status Kesepakatan: ').bold = True
    p_meta.add_run('Telah Disetujui (Siap Eksekusi Teknis)\n')
    p_meta.add_run('Topik: ').bold = True
    p_meta.add_run('Sembunyikan 3 Menu Navigasi, Kolom Search Katalog, Pemisahan Tab & CMS Teknologi Frame vs Lensa')

    # Divider line
    p_div = doc.add_paragraph()
    p_div.paragraph_format.space_before = Pt(2)
    p_div.paragraph_format.space_after = Pt(8)
    div_run = p_div.add_run('—' * 65)
    div_run.font.color.rgb = RGBColor(203, 213, 225)

    # Section 1: Ringkasan Eksekutif
    s1_title = doc.add_paragraph()
    s1_title.paragraph_format.space_before = Pt(3)
    s1_title.paragraph_format.space_after = Pt(3)
    s1_run = s1_title.add_run('1. Ringkasan Eksekutif Poin Masukan')
    s1_run.bold = True
    s1_run.font.size = Pt(11)
    s1_run.font.color.rgb = RGBColor(15, 23, 42)

    # Summary Table
    table_sum = doc.add_table(rows=1, cols=3)
    table_sum.alignment = WD_TABLE_ALIGNMENT.CENTER
    table_sum.style = 'Table Grid'
    table_sum.autofit = False

    col_widths = [Inches(0.5), Inches(2.3), Inches(4.2)]
    hdr_cells = table_sum.rows[0].cells
    hdr_titles = ['No', 'Komponen / Bagian', 'Kesepakatan & Kebutuhan Klien (Bram)']
    prevent_row_split(table_sum.rows[0])
    for i, t in enumerate(hdr_titles):
        hdr_cells[i].width = col_widths[i]
        set_cell_background(hdr_cells[i], '1E293B')
        set_cell_margins(hdr_cells[i], top=70, bottom=70, left=90, right=90)
        p = hdr_cells[i].paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER if i == 0 else WD_ALIGN_PARAGRAPH.LEFT
        run = p.add_run(t)
        run.bold = True
        run.font.size = Pt(9)
        run.font.color.rgb = RGBColor(255, 255, 255)

    sum_items = [
        ('1', 'Header & Footer Navigation', 'Sembunyikan (HIDE) tautan menu "Profil MGISPEC", "Teknologi MGISPEC", dan "Sertifikasi MGISPEC" dari navigasi publik sampai halamannya siap.'),
        ('2', 'Halaman Katalog (/katalog)', 'Tambahkan Kolom Pencarian (Search Bar) di kanan atas katalog untuk mempermudah mencari produk berdasarkan nama model atau SKU.'),
        ('3', 'Front-End Detail Produk', 'Pecah tab "Teknologi" menjadi 2 Tab Sejajar: [Teknologi Frame] dan [Teknologi Lensa], masing-masing tampil dalam format kartu grid 2 kolom (desktop) & 1 kolom (mobile).'),
        ('4', 'Panel Admin (CMS)', 'Di Menu Produk > Tab "Spesifikasi & Teknologi", bagi menjadi 3 repeater terpisah: Spesifikasi Dimensi, Teknologi Frame (12 item + tombol preset otomatis), dan Teknologi Lensa (4, 6, atau 9 item).'),
        ('5', 'Fleksibilitas Ikon CMS', 'Sistem ikon pada CMS fleksibel dan dinamis (bebas memilih/mengganti icon Font Awesome sesuai kebutuhan, tidak terpaku pada ikon bawaan).')
    ]

    for no, comp, desc in sum_items:
        row = table_sum.add_row()
        prevent_row_split(row)
        rc = row.cells
        for i, val in enumerate([no, comp, desc]):
            rc[i].width = col_widths[i]
            set_cell_margins(rc[i], top=60, bottom=60, left=90, right=90)
            p = rc[i].paragraphs[0]
            p.paragraph_format.line_spacing = 1.08
            if i == 0:
                p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            else:
                p.alignment = WD_ALIGN_PARAGRAPH.LEFT
            run = p.add_run(val)
            run.font.size = Pt(8.6)
            if i < 2:
                run.bold = True

    # Spacing
    doc.add_paragraph('').paragraph_format.space_after = Pt(2)

    # Section 2: Rincian Solusi Teknis & Struktur Antarmuka
    s2_title = doc.add_paragraph()
    s2_title.paragraph_format.space_before = Pt(4)
    s2_title.paragraph_format.space_after = Pt(3)
    s2_run = s2_title.add_run('2. Rincian Teknis & Desain Solusi')
    s2_run.bold = True
    s2_run.font.size = Pt(11)
    s2_run.font.color.rgb = RGBColor(15, 23, 42)

    # Point A: Hide Navigation
    p_a = doc.add_paragraph()
    p_a.paragraph_format.space_before = Pt(2)
    p_a.paragraph_format.space_after = Pt(1)
    p_a_r = p_a.add_run('A. Sembunyikan (Hide) 3 Menu Halaman di Header & Footer')
    p_a_r.bold = True
    p_a_r.font.size = Pt(9.5)
    
    b_a = [
        'Kondisi: Pada header & footer sebelumnya tercantum link ke Profil, Teknologi, dan Sertifikasi.',
        'Instruksi Klien: Sembunyikan (hide) ketiga menu tersebut.',
        'Penerapan: Menonaktifkan link tersebut di resources/views/partials/header.blade.php dan footer.blade.php sehingga menu utama publik hanya: Beranda, Katalog MGISPEC, Kontak MGISPEC, Language Switcher, dan Login Member.'
    ]
    for b in b_a:
        bp = doc.add_paragraph(b, style='List Bullet')
        bp.paragraph_format.space_after = Pt(1)
        bp.paragraph_format.line_spacing = 1.08
        bp.runs[0].font.size = Pt(8.8)

    # Point B: Search Bar
    p_b = doc.add_paragraph()
    p_b.paragraph_format.space_before = Pt(3)
    p_b.paragraph_format.space_after = Pt(1)
    p_b_r = p_b.add_run('B. Penambahan Fitur Pencarian (Search Bar) di Halaman Katalog')
    p_b_r.bold = True
    p_b_r.font.size = Pt(9.5)
    
    b_b = [
        'Kebutuhan: Klien menanyakan ketersediaan kolom search di area kanan atas katalog.',
        'Penerapan: Menambahkan komponen search box input dengan ikon kaca pembesar di atas grid katalog. Bekerja secara responsif dan instan untuk memfilter kacamata berdasarkan nama produk atau nomor SKU.'
    ]
    for b in b_b:
        bp = doc.add_paragraph(b, style='List Bullet')
        bp.paragraph_format.space_after = Pt(1)
        bp.paragraph_format.line_spacing = 1.08
        bp.runs[0].font.size = Pt(8.8)

    # Point C: Front-End Tab Layout
    p_c = doc.add_paragraph()
    p_c.paragraph_format.space_before = Pt(3)
    p_c.paragraph_format.space_after = Pt(1)
    p_c_r = p_c.add_run('C. Tata Letak Front-End Halaman Detail Produk (/katalog/{slug})')
    p_c_r.bold = True
    p_c_r.font.size = Pt(9.5)
    
    b_c = [
        'Letak Tampilan: Tab ini tampil saat pengunjung membuka salah satu kacamata di katalog (bukan di Beranda).',
        'Struktur Tab Sejajar Baru: [Deskripsi] | [Spesifikasi] | [Teknologi Frame] | [Teknologi Lensa].',
        'Tampilan Isi Tab: Setiap item teknologi yang diinput muncul sebagai kotak kartu (Card) berikon lengkap dengan judul & ulasannya. Di desktop tersusun dalam grid 2 kolom, dan di HP otomatis menjadi 1 kolom ke bawah.',
        'Pemisahan Kebutuhan Data: Tab [Teknologi Frame] menampilkan 12 material/fitur frame (SiO₂, Graphene, FIR, dll). Tab [Teknologi Lensa] menampilkan 4, 6, atau 9 fitur lensa khusus model tersebut.'
    ]
    for b in b_c:
        bp = doc.add_paragraph(b, style='List Bullet')
        bp.paragraph_format.space_after = Pt(1)
        bp.paragraph_format.line_spacing = 1.08
        bp.runs[0].font.size = Pt(8.8)

    # Point D: CMS Admin Structure
    p_d = doc.add_paragraph()
    p_d.paragraph_format.space_before = Pt(3)
    p_d.paragraph_format.space_after = Pt(1)
    p_d_r = p_d.add_run('D. Tata Letak & Alur Pengelolaan di Panel Admin (CMS Filament)')
    p_d_r.bold = True
    p_d_r.font.size = Pt(9.5)
    
    b_d = [
        'Lokasi di CMS: Menu Produk (Products) > Klik "Edit" atau "Tambah Produk" > Buka Tab [Spesifikasi & Teknologi].',
        'Struktur 3 Kelompok Input di CMS:\n'
        '  1. Kelompok "Spesifikasi Frame": Mengatur dimensi fisik (Lebar lensa, bridge, tinggi, panjang kaki frame).\n'
        '  2. Kelompok "Teknologi & Material Frame" (BARU): Repeater khusus untuk menginput s/d 12 material nano frame, dilengkapi tombol preset "+ Muat 12 Material Standar MGISpec" agar admin tidak perlu mengetik ulang dari awal.\n'
        '  3. Kelompok "Teknologi Lensa" (BARU): Repeater khusus untuk menginput fitur lensa produk (4, 6, atau 9 item).',
        'Fleksibilitas Ikon: Setiap item repeater menyediakan input kelas icon Font Awesome (misal: fa-solid fa-atom, fa-solid fa-shield-virus, fa-solid fa-wind) sehingga icon bebas disesuaikan dan tidak statis.'
    ]
    for b in b_d:
        bp = doc.add_paragraph(b, style='List Bullet')
        bp.paragraph_format.space_after = Pt(1)
        bp.paragraph_format.line_spacing = 1.08
        bp.runs[0].font.size = Pt(8.8)

    # Section 3: Action Plan
    s3_title = doc.add_paragraph()
    s3_title.paragraph_format.space_before = Pt(5)
    s3_title.paragraph_format.space_after = Pt(2)
    s3_run = s3_title.add_run('3. Checklist Eksekusi Teknis (Saat Siap Dikerjakan)')
    s3_run.bold = True
    s3_run.font.size = Pt(11)
    s3_run.font.color.rgb = RGBColor(15, 23, 42)

    actions = [
        ('[ ] Sembunyikan link "Profil MGISPEC", "Teknologi MGISPEC", dan "Sertifikasi MGISPEC" di views/partials/header.blade.php & footer.blade.php.'),
        ('[ ] Tambahkan komponen Search Bar responsif di atas grid produk katalog (views/catalog/index.blade.php).'),
        ('[ ] Update skema migrasi database & model Product: pisahkan kolom JSON frame_technologies dan lens_technologies.'),
        ('[ ] Update Filament CMS (ProductResource.php): buat 2 repeater terpisah di tab "Spesifikasi & Teknologi" + tombol preset 12 material + input icon fleksibel.'),
        ('[ ] Update view detail produk (views/catalog/show.blade.php): ubah menjadi tab sejajar [Teknologi Frame] dan [Teknologi Lensa] dengan layout kartu grid responsif.'),
        ('[ ] Migrasikan data 12 teknologi frame & variasi lensa pada 46 katalog kacamata aktif.')
    ]
    for act in actions:
        bp = doc.add_paragraph(act, style='List Bullet')
        bp.paragraph_format.space_after = Pt(1.5)
        bp.paragraph_format.line_spacing = 1.08
        bp.runs[0].font.size = Pt(8.8)

    # Note
    p_note = doc.add_paragraph()
    p_note.paragraph_format.space_before = Pt(8)
    p_note.add_run('Catatan: Kode program belum dieksekusi ke server, menunggu konfirmasi jadwal pengerjaan selanjutnya.')
    p_note.runs[0].italic = True
    p_note.runs[0].font.size = Pt(8.5)
    p_note.runs[0].font.color.rgb = RGBColor(100, 116, 139)

    output_docx = 'Feedback_MGISpec_27102029.docx'
    doc.save(output_docx)
    print(f'Document saved successfully as: {output_docx}')

    # Convert to PDF
    cmd = [
        '/Applications/LibreOffice.app/Contents/MacOS/soffice',
        '--headless',
        '--convert-to',
        'pdf',
        output_docx,
        '--outdir',
        '.'
    ]
    try:
        res = subprocess.run(cmd, capture_output=True, text=True, check=True)
        print('PDF generated successfully as: Feedback_MGISpec_27102029.pdf')
    except Exception as e:
        print(f'Warning: PDF conversion failed: {e}')

if __name__ == '__main__':
    create_feedback_doc()
