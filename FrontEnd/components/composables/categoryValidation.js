import * as yup from 'yup'; // Thêm dòng này nếu chưa có

export const categoryValidation = (existingCategories = []) => {
  const validationSchema = yup.object({
    name: yup
      .string()
      .required('Tên danh mục là bắt buộc')
      .min(3, 'Tên danh mục phải có ít nhất 3 ký tự')
      .notOneOf(existingCategories, 'Tên danh mục đã tồn tại'),
    description: yup
      .string()
      .required('Mô tả là bắt buộc')
      .min(10, 'Mô tả phải có ít nhất 10 ký tự'),
    type_id: yup
      .string()
      .required('Kiểu là bắt buộc'),
  });

  return { validationSchema };
};
