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

def set_cell_margins(cell, top=65, bottom=65, left=100, right=100):
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

def create_proposal():
    doc = docx.Document()

    # Page Margins: 0.6 in top/bottom, 0.8 in left/right (Compact 1 Page)
    for section in doc.sections:
        section.top_margin = Inches(0.6)
        section.bottom_margin = Inches(0.6)
        section.left_margin = Inches(0.8)
        section.right_margin = Inches(0.8)

    # Document Header / Title
    title = doc.add_paragraph()
    title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    title.paragraph_format.space_before = Pt(0)
    title.paragraph_format.space_after = Pt(2)
    
    r1 = title.add_run('PROPOSAL PENAWARAN BIAYA TAMBAHAN (ADDENDUM)\n')
    r1.bold = True
    r1.font.size = Pt(11.5)
    r1.font.color.rgb = RGBColor(30, 41, 59)

    r2 = title.add_run('PENGEMBANGAN 3 HALAMAN KHUSUS & INTEGRASI CMS\nWEBSITE KATALOG PRODUK MGISPEC')
    r2.bold = True
    r2.font.size = Pt(12.5)
    r2.font.color.rgb = RGBColor(15, 23, 42)

    # Addressee
    p_addr = doc.add_paragraph()
    p_addr.paragraph_format.space_before = Pt(8)
    p_addr.paragraph_format.space_after = Pt(2)
    p_addr.paragraph_format.line_spacing = 1.1
    p_addr.add_run('Kepada Yth.\n').font.size = Pt(10)
    run_name = p_addr.add_run('Bpk Bram Setiawan Liem\n')
    run_name.bold = True
    run_name.font.size = Pt(10)
    p_addr.add_run('MGISpec / MGIClub Global — Di Tempat').font.size = Pt(10)

    p_sub = doc.add_paragraph()
    p_sub.paragraph_format.space_before = Pt(3)
    p_sub.paragraph_format.space_after = Pt(4)
    sub_run = p_sub.add_run('Perihal: Penawaran Biaya Tambahan 3 Halaman Khusus (Profile, Teknologi, Sertifikasi) & CMS')
    sub_run.bold = True
    sub_run.font.size = Pt(10)
    sub_run.font.color.rgb = RGBColor(15, 23, 42)

    # Divider line
    p_div = doc.add_paragraph()
    p_div.paragraph_format.space_before = Pt(0)
    p_div.paragraph_format.space_after = Pt(8)
    div_run = p_div.add_run('—' * 65)
    div_run.font.color.rgb = RGBColor(203, 213, 225)

    # Section 1: Ruang Lingkup Pekerjaan Tambahan
    s1_title = doc.add_paragraph()
    s1_title.paragraph_format.space_before = Pt(4)
    s1_title.paragraph_format.space_after = Pt(3)
    s1_run = s1_title.add_run('1. Ruang Lingkup Pekerjaan Tambahan (Scope of Work)')
    s1_run.bold = True
    s1_run.font.size = Pt(10.5)
    s1_run.font.color.rgb = RGBColor(15, 23, 42)

    p_scope_intro = doc.add_paragraph(
        'Penambahan 3 (tiga) halaman khusus baru pada website katalog MGISpec dengan design environment, tata letak responsif (desktop & mobile), dan dukungan dual bahasa (ID & EN) mengikuti sistem yang sudah berjalan, serta terintegrasi ke CMS:'
    )
    p_scope_intro.paragraph_format.line_spacing = 1.12
    p_scope_intro.paragraph_format.space_after = Pt(4)
    p_scope_intro.runs[0].font.size = Pt(9.5)

    pages = [
        ('1. Halaman MGISPEC Profile (/profil)', 'Profil resmi brand, sejarah, visi misi, dan standar mutu MGISpec.'),
        ('2. Halaman MGISPEC Technologies (/teknologi)', 'Edukasi sains teknologi lensa, sirkulasi oksigen, dan 10 material canggih.'),
        ('3. Halaman MGISPEC Certifications (/sertifikasi)', 'Galeri legalitas medis & sertifikasi mutu internasional (fitur lightbox zoom).'),
        ('4. Integrasi Navigasi & CMS Admin', 'Penyambungan menu Header & Footer serta modul kelola konten di panel admin CMS.')
    ]
    for title_p, desc_p in pages:
        bp = doc.add_paragraph(style='List Bullet')
        bp.paragraph_format.space_after = Pt(2)
        bp.paragraph_format.line_spacing = 1.12
        r_title = bp.add_run(f'{title_p}: ')
        r_title.bold = True
        r_title.font.size = Pt(9.2)
        r_desc = bp.add_run(desc_p)
        r_desc.font.size = Pt(9.2)

    # Section 2: Rencana Anggaran Biaya
    s2_title = doc.add_paragraph()
    s2_title.paragraph_format.space_before = Pt(10)
    s2_title.paragraph_format.space_after = Pt(4)
    s2_run = s2_title.add_run('2. Rencana Anggaran Biaya (RAB)')
    s2_run.bold = True
    s2_run.font.size = Pt(10.5)
    s2_run.font.color.rgb = RGBColor(15, 23, 42)

    # Table (Single Row / Lump Sum Package, without per-item breakdown)
    table = doc.add_table(rows=1, cols=3)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.style = 'Table Grid'
    table.autofit = False

    # Widths: No: 0.5 in, Deskripsi: 4.8 in, Biaya: 1.7 in (Total 7.0 in)
    col_widths = [Inches(0.5), Inches(4.8), Inches(1.7)]

    # Header Row
    hdr_cells = table.rows[0].cells
    hdr_titles = ['No', 'Deskripsi Pekerjaan Tambahan', 'Biaya (Rp)']
    prevent_row_split(table.rows[0])
    for i, title_text in enumerate(hdr_titles):
        hdr_cells[i].width = col_widths[i]
        set_cell_background(hdr_cells[i], '1E293B')  # Dark slate
        set_cell_margins(hdr_cells[i], top=70, bottom=70, left=100, right=100)
        p = hdr_cells[i].paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER if i != 1 else WD_ALIGN_PARAGRAPH.LEFT
        run = p.add_run(title_text)
        run.bold = True
        run.font.size = Pt(9)
        run.font.color.rgb = RGBColor(255, 255, 255)

    # Single Row Package (No breakdown per item)
    row = table.add_row()
    prevent_row_split(row)
    row_cells = row.cells
    
    # Col 0: No
    row_cells[0].width = col_widths[0]
    set_cell_margins(row_cells[0], top=70, bottom=70, left=100, right=100)
    p0 = row_cells[0].paragraphs[0]
    p0.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r0 = p0.add_run('1')
    r0.bold = True
    r0.font.size = Pt(9.5)

    # Col 1: Description
    row_cells[1].width = col_widths[1]
    set_cell_margins(row_cells[1], top=70, bottom=70, left=100, right=100)
    p1 = row_cells[1].paragraphs[0]
    p1.paragraph_format.line_spacing = 1.15
    r_item_title = p1.add_run('Pengembangan 3 Halaman Khusus & Integrasi Panel CMS Admin\n')
    r_item_title.bold = True
    r_item_title.font.size = Pt(9.2)

    r_item_desc = p1.add_run(
        '• Pembuatan Halaman MGISPEC Profile (/profil)\n'
        '• Pembuatan Halaman MGISPEC Technologies (/teknologi)\n'
        '• Pembuatan Halaman MGISPEC Certifications (/sertifikasi)\n'
        '• Pembaruan navigasi menu Header & Footer\n'
        '• Integrasi pengelolaan konten via panel CMS Admin\n'
        'Catatan: Design environment & visual styling mengikuti sistem website yang ada, responsif (desktop & mobile), serta mendukung dual language (ID & EN).'
    )
    r_item_desc.font.size = Pt(8.8)

    # Col 2: Cost
    row_cells[2].width = col_widths[2]
    set_cell_margins(row_cells[2], top=70, bottom=70, left=100, right=100)
    p2 = row_cells[2].paragraphs[0]
    p2.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    r2_cost = p2.add_run('2.000.000')
    r2_cost.font.size = Pt(9.5)

    # Total Row
    total_row = table.add_row()
    prevent_row_split(total_row)
    total_row_cells = total_row.cells
    for i in range(3):
        total_row_cells[i].width = col_widths[i]
        set_cell_margins(total_row_cells[i], top=75, bottom=75, left=100, right=100)
        set_cell_background(total_row_cells[i], 'F1F5F9')

    total_row_cells[0].paragraphs[0].text = ''
    p_tot_lbl = total_row_cells[1].paragraphs[0]
    p_tot_lbl.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    run_tot_lbl = p_tot_lbl.add_run('TOTAL BIAYA TAMBAHAN:')
    run_tot_lbl.bold = True
    run_tot_lbl.font.size = Pt(9.5)
    run_tot_lbl.font.color.rgb = RGBColor(15, 23, 42)

    p_tot_val = total_row_cells[2].paragraphs[0]
    p_tot_val.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    run_tot_val = p_tot_val.add_run('Rp 2.000.000,-')
    run_tot_val.bold = True
    run_tot_val.font.size = Pt(10)
    run_tot_val.font.color.rgb = RGBColor(16, 185, 129)

    # Terbilang note
    p_terbilang = doc.add_paragraph()
    p_terbilang.paragraph_format.space_before = Pt(2)
    p_terbilang.paragraph_format.space_after = Pt(6)
    p_terbilang.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    run_tb = p_terbilang.add_run('(Terbilang: Dua Juta Rupiah)')
    run_tb.italic = True
    run_tb.font.size = Pt(8.5)
    run_tb.font.color.rgb = RGBColor(100, 116, 139)

    # Section 3: Timeline & Ketentuan Pembayaran
    s3_title = doc.add_paragraph()
    s3_title.paragraph_format.space_before = Pt(4)
    s3_title.paragraph_format.space_after = Pt(2)
    s3_run = s3_title.add_run('3. Estimasi Waktu & Ketentuan Pembayaran')
    s3_run.bold = True
    s3_run.font.size = Pt(10.5)
    s3_run.font.color.rgb = RGBColor(15, 23, 42)

    p_terms = doc.add_paragraph(
        '• Waktu Pengerjaan: 3 – 5 hari kerja setelah materi narasi dan berkas sertifikat disetujui.\n'
        '• Pembayaran: Pembayaran Rp 2.000.000,- dilakukan saat ketiga halaman selesai diintegrasikan dan diverifikasi (atau digabungkan dengan pelunasan akhir kontrak utama).'
    )
    p_terms.paragraph_format.line_spacing = 1.12
    p_terms.paragraph_format.space_after = Pt(6)
    p_terms.runs[0].font.size = Pt(9)

    # Closing & Signature
    p_close = doc.add_paragraph(
        'Demikian penawaran biaya tambahan ini kami sampaikan. Terima kasih atas kerja samanya.'
    )
    p_close.paragraph_format.line_spacing = 1.12
    p_close.paragraph_format.space_after = Pt(10)
    p_close.runs[0].font.size = Pt(9)

    p_sig = doc.add_paragraph()
    p_sig.paragraph_format.space_before = Pt(0)
    p_sig.paragraph_format.keep_with_next = True
    p_sig.add_run('Hormat kami,\n\n\n').font.size = Pt(9.5)
    run_sig_name = p_sig.add_run('(Andreas Didit M.)\n')
    run_sig_name.bold = True
    run_sig_name.font.size = Pt(9.5)
    run_sig_role = p_sig.add_run('Web & Systems Developer')
    run_sig_role.font.size = Pt(8.5)
    run_sig_role.font.color.rgb = RGBColor(100, 116, 139)

    output_docx = 'Penawaran_Biaya_Tambahan_3_Page_MGISpec.docx'
    doc.save(output_docx)
    print(f'Document saved successfully as: {output_docx}')

    # Convert to PDF using LibreOffice
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
        print('PDF Conversion output:', res.stdout)
        print('PDF generated successfully as: Penawaran_Biaya_Tambahan_3_Page_MGISpec.pdf')
    except Exception as e:
        print(f'Warning: PDF conversion failed: {e}')

if __name__ == '__main__':
    create_proposal()
