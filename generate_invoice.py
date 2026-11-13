import zlib
import re
import os
import docx
from docx.shared import Pt, Inches, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import parse_xml, OxmlElement
from docx.oxml.ns import nsdecls, qn

def generate_pdf():
    # Read the original reference invoice
    with open('Invoice_MGI_Spec_Originals_Check.pdf', 'rb') as f:
        orig = f.read()

    # Extract objects from original
    matches = list(re.finditer(rb'(\d+)\s+0\s+obj\b', orig))
    objs = {}
    for i, m in enumerate(matches):
        obj_num = int(m.group(1))
        start = m.start()
        end_obj = orig.find(b'endobj', start) + 6
        objs[obj_num] = orig[start:end_obj]

    # New content stream for PDF
    content_text = r"""2 J
0.57 w
BT /F1 16.00 Tf ET
BT 264.74 794.57 Td (INVOICE) Tj ET
BT /F2 12.00 Tf ET
BT 31.18 744.75 Td (No. Invoice:) Tj ET
BT 144.57 744.75 Td (INV-20261104-001) Tj ET
BT 31.18 727.74 Td (Tanggal:) Tj ET
BT 144.57 727.74 Td (04 November 2026) Tj ET
BT /F1 12.00 Tf ET
BT 31.18 696.56 Td (Kepada Yth.) Tj ET
BT /F2 12.00 Tf ET
BT 31.18 679.55 Td (MGI Spec) Tj ET
BT 31.18 662.54 Td (a.n Bpk Bram Setiawan Liem) Tj ET
BT /F1 11.00 Tf ET
28.35 629.29 28.35 -22.68 re S BT 35.19 614.65 Td (No) Tj ET
56.69 629.29 368.50 -22.68 re S BT 188.67 614.65 Td (Deskripsi Pekerjaan) Tj ET
425.20 629.29 42.52 -22.68 re S BT 436.98 614.65 Td (Jml) Tj ET
467.72 629.29 99.21 -22.68 re S BT 474.54 614.65 Td (Total Harga \(Rp\)) Tj ET
BT /F2 11.00 Tf ET
28.35 606.61 28.35 -42.52 re S BT 39.46 582.05 Td (1) Tj ET
56.69 564.09 368.50 42.52 re S
BT 59.53 592.68 Td (Pelunasan \(Tahap II - 50%\) Pembuatan Website Katalog Produk) Tj ET
BT 59.53 571.42 Td (Kacamata dan CMS \(Admin\)) Tj ET
425.20 606.61 42.52 -42.52 re S BT 443.40 582.05 Td (1) Tj ET
467.72 606.61 99.21 -42.52 re S BT 515.15 582.05 Td (2.500.000) Tj ET
BT /F1 11.00 Tf ET
28.35 564.09 439.37 -22.68 re S BT 428.22 549.46 Td (TOTAL) Tj ET
467.72 564.09 99.21 -22.68 re S BT 515.15 549.46 Td (2.500.000) Tj ET
BT /F3 11.00 Tf ET
BT 31.18 472.92 Td (Pembayaran dapat ditransfer ke:) Tj ET
BT /F1 12.00 Tf ET
BT 31.18 455.61 Td (BCA 4650263460) Tj ET
BT /F2 11.00 Tf ET
BT 31.18 438.90 Td (a.n. Andreas Didit Mifanto) Tj ET
BT /F2 12.00 Tf ET
BT 447.55 472.62 Td (Techno DNA) Tj ET
q 85.04 0 0 85.37 439.37 396.52 cm /I1 Do Q
BT 439.21 384.75 Td (Andreas Didit M) Tj ET 439.21 383.55 85.36 -0.60 re f
"""
    comp_stream = zlib.compress(content_text.strip().encode('latin1') + b"\n")
    new_obj4 = f"""4 0 obj
<<
/Filter /FlateDecode
/Length {len(comp_stream)}
>>
stream
""".encode('latin1') + comp_stream + b"\nendstream\nendobj"
    objs[4] = new_obj4

    # Update CreationDate in Obj 11
    objs[11] = b"""11 0 obj
<<
/CreationDate (D:20261104134712Z)
>>
endobj"""

    # Assemble PDF
    out = bytearray()
    out.extend(b"%PDF-1.4\n%\xe2\xe3\xcf\xd3\n")
    
    offsets = {}
    for num in range(1, 12):
        offsets[num] = len(out)
        out.extend(objs[num])
        out.extend(b"\n")
    
    # xref table
    startxref = len(out)
    out.extend(b"xref\n0 12\n")
    out.extend(b"0000000000 65535 f \n")
    for num in range(1, 12):
        out.extend(f"{offsets[num]:010d} 00000 n \n".encode('latin1'))
    
    # trailer
    out.extend(f"""trailer
<<
/Size 12
/Root 2 0 R
/Info 11 0 R
/ID [<2164C41046330E11330B00AE37C2E541><2164C41046330E11330B00AE37C2E541>]
>>
startxref
{startxref}
%%EOF
""".encode('latin1'))

    out_filename = 'Invoice_MGISpec_Pelunasan_Website_Katalog.pdf'
    with open(out_filename, 'wb') as f:
        f.write(out)
    print(f"Generated {out_filename} ({len(out)} bytes)")

def set_cell_margins(cell, top=100, bottom=100, left=120, right=120):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('top', top), ('bottom', bottom), ('left', left), ('right', right)]:
        node = OxmlElement(f'w:{m}')
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def generate_docx():
    doc = docx.Document()
    for s in doc.sections:
        s.top_margin = Inches(0.6)
        s.bottom_margin = Inches(0.6)
        s.left_margin = Inches(0.4)
        s.right_margin = Inches(0.4)

    # Title
    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_before = Pt(0)
    p_title.paragraph_format.space_after = Pt(24)
    r = p_title.add_run('INVOICE')
    r.bold = True
    r.font.name = 'Helvetica'
    r.font.size = Pt(16)

    # Info block
    table_info = doc.add_table(rows=2, cols=2)
    table_info.autofit = False
    table_info.columns[0].width = Inches(1.5)
    table_info.columns[1].width = Inches(5.5)
    
    info_data = [
        ("No. Invoice:", "INV-20261104-001"),
        ("Tanggal:", "04 November 2026")
    ]
    for row_idx, (k, v) in enumerate(info_data):
        row = table_info.rows[row_idx]
        p0 = row.cells[0].paragraphs[0]
        p0.paragraph_format.space_after = Pt(2)
        r0 = p0.add_run(k)
        r0.font.name = 'Helvetica'
        r0.font.size = Pt(11)
        
        p1 = row.cells[1].paragraphs[0]
        p1.paragraph_format.space_after = Pt(2)
        r1 = p1.add_run(v)
        r1.font.name = 'Helvetica'
        r1.font.size = Pt(11)

    p_space = doc.add_paragraph()
    p_space.paragraph_format.space_before = Pt(8)
    p_space.paragraph_format.space_after = Pt(0)
    
    p_yth = doc.add_paragraph()
    p_yth.paragraph_format.space_after = Pt(2)
    r_yth = p_yth.add_run('Kepada Yth.')
    r_yth.bold = True
    r_yth.font.name = 'Helvetica'
    r_yth.font.size = Pt(11)

    p_client = doc.add_paragraph()
    p_client.paragraph_format.space_after = Pt(24)
    r_c1 = p_client.add_run('MGI Spec\na.n Bpk Bram Setiawan Liem')
    r_c1.font.name = 'Helvetica'
    r_c1.font.size = Pt(11)

    # Table Item
    table = doc.add_table(rows=1, cols=4)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.style = 'Table Grid'
    table.autofit = False

    col_widths = [Inches(0.4), Inches(5.1), Inches(0.6), Inches(1.4)]

    # Header
    hdr = table.rows[0].cells
    hdr_titles = ['No', 'Deskripsi Pekerjaan', 'Jml', 'Total Harga (Rp)']
    for i, t in enumerate(hdr_titles):
        hdr[i].width = col_widths[i]
        set_cell_margins(hdr[i], top=80, bottom=80, left=80, right=80)
        p = hdr[i].paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        run = p.add_run(t)
        run.bold = True
        run.font.name = 'Helvetica'
        run.font.size = Pt(10)

    # Row 1 (Item)
    r1 = table.add_row().cells
    for i in range(4):
        r1[i].width = col_widths[i]
        set_cell_margins(r1[i], top=120, bottom=120, left=80, right=80)

    # Col 0 (No)
    p0 = r1[0].paragraphs[0]
    p0.alignment = WD_ALIGN_PARAGRAPH.CENTER
    rn0 = p0.add_run('1')
    rn0.font.name = 'Helvetica'
    rn0.font.size = Pt(10)

    # Col 1 (Deskripsi)
    p1 = r1[1].paragraphs[0]
    p1.paragraph_format.line_spacing = 1.2
    rn1_a = p1.add_run('Pelunasan (Tahap II - 50%) Pembuatan Website Katalog Produk\n')
    rn1_a.font.name = 'Helvetica'
    rn1_a.font.size = Pt(10)
    rn1_b = p1.add_run('Kacamata dan CMS (Admin)')
    rn1_b.font.name = 'Helvetica'
    rn1_b.font.size = Pt(10)

    # Col 2 (Jml)
    p2 = r1[2].paragraphs[0]
    p2.alignment = WD_ALIGN_PARAGRAPH.CENTER
    rn2 = p2.add_run('1')
    rn2.font.name = 'Helvetica'
    rn2.font.size = Pt(10)

    # Col 3 (Total Harga)
    p3 = r1[3].paragraphs[0]
    p3.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    rn3 = p3.add_run('2.500.000')
    rn3.font.name = 'Helvetica'
    rn3.font.size = Pt(10)

    # Total Row
    r_tot = table.add_row().cells
    for i in range(4):
        r_tot[i].width = col_widths[i]
        set_cell_margins(r_tot[i], top=80, bottom=80, left=80, right=80)

    # Merge col 0, 1, 2
    r_tot[0].merge(r_tot[1]).merge(r_tot[2])
    p_tot_lbl = r_tot[0].paragraphs[0]
    p_tot_lbl.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    run_tot_lbl = p_tot_lbl.add_run('TOTAL')
    run_tot_lbl.bold = True
    run_tot_lbl.font.name = 'Helvetica'
    run_tot_lbl.font.size = Pt(10)

    p_tot_val = r_tot[3].paragraphs[0]
    p_tot_val.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    run_tot_val = p_tot_val.add_run('2.500.000')
    run_tot_val.bold = True
    run_tot_val.font.name = 'Helvetica'
    run_tot_val.font.size = Pt(10)

    # Bottom layout: 2-column table for Payment Info (Left) and Signature (Right)
    doc.add_paragraph().paragraph_format.space_before = Pt(20)

    table_bottom = doc.add_table(rows=1, cols=2)
    table_bottom.autofit = False
    table_bottom.columns[0].width = Inches(4.5)
    table_bottom.columns[1].width = Inches(3.0)

    # Left cell: Bank info
    cell_bank = table_bottom.rows[0].cells[0]
    pb1 = cell_bank.paragraphs[0]
    pb1.paragraph_format.space_after = Pt(3)
    rb1 = pb1.add_run('Pembayaran dapat ditransfer ke:')
    rb1.italic = True
    rb1.font.name = 'Helvetica'
    rb1.font.size = Pt(10)

    pb2 = cell_bank.add_paragraph()
    pb2.paragraph_format.space_after = Pt(2)
    rb2 = pb2.add_run('BCA 4650263460')
    rb2.bold = True
    rb2.font.name = 'Helvetica'
    rb2.font.size = Pt(11)

    pb3 = cell_bank.add_paragraph()
    rb3 = pb3.add_run('a.n. Andreas Didit Mifanto')
    rb3.font.name = 'Helvetica'
    rb3.font.size = Pt(10)

    # Right cell: Signature
    cell_sig = table_bottom.rows[0].cells[1]
    ps1 = cell_sig.paragraphs[0]
    ps1.alignment = WD_ALIGN_PARAGRAPH.CENTER
    ps1.paragraph_format.space_after = Pt(4)
    rs1 = ps1.add_run('Techno DNA')
    rs1.font.name = 'Helvetica'
    rs1.font.size = Pt(11)

    # Signature Image
    ps2 = cell_sig.add_paragraph()
    ps2.alignment = WD_ALIGN_PARAGRAPH.CENTER
    ps2.paragraph_format.space_after = Pt(4)
    if os.path.exists('signature.png'):
        ps2.add_run().add_picture('signature.png', width=Inches(1.2))

    ps3 = cell_sig.add_paragraph()
    ps3.alignment = WD_ALIGN_PARAGRAPH.CENTER
    rs3 = ps3.add_run('Andreas Didit M')
    rs3.underline = True
    rs3.font.name = 'Helvetica'
    rs3.font.size = Pt(10.5)

    docx_filename = 'Invoice_MGISpec_Pelunasan_Website_Katalog.docx'
    doc.save(docx_filename)
    print(f"Generated {docx_filename}")

if __name__ == '__main__':
    generate_pdf()
    generate_docx()
